<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Get all users with pagination and filters
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = User::query();

            // Apply filters
            if ($request->has('role')) {
                $query->role($request->role);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($request->has('status')) {
                if ($request->status === 'active') {
                    $query->whereNotNull('email_verified_at');
                } elseif ($request->status === 'inactive') {
                    $query->whereNull('email_verified_at');
                }
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $users = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $users,
                'message' => 'Users retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new user
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|in:Admin,Manager,User',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userData = $validator->validated();
            unset($userData['avatar']); // Remove avatar from user data
            
            // Extract role for assignment
            $roleToAssign = $userData['role'] ?? null;
            unset($userData['role']); // Remove role from user data since it's not in fillable

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $avatarName = time() . '_' . Str::random(10) . '.' . $avatar->getClientOriginalExtension();
                $avatarPath = 'avatars/' . date('Y/m/d') . '/' . $avatarName;
                
                Storage::disk('public')->put($avatarPath, file_get_contents($avatar));
                $userData['avatar_url'] = $avatarPath;
            }

            // Hash password
            $userData['password'] = Hash::make($userData['password']);

            // Create user
            $user = User::create($userData);

            // Assign role using Spatie Permission
            if ($roleToAssign) {
                $role = \Spatie\Permission\Models\Role::where('name', $roleToAssign)->first();
                if ($role) {
                    $user->assignRole($role);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $user->load('roles'),
                'message' => 'User created successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific user
     */
    public function show($id): JsonResponse
    {
        try {
            $user = User::with(['roles', 'permissions'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User retrieved successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a user
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => [
                    'sometimes',
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($id)
                ],
                'role' => 'sometimes|required|in:Admin,Manager,User',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'email_verified_at' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userData = $validator->validated();
            unset($userData['avatar']); // Remove avatar from user data
            
            // Extract role for assignment
            $roleToAssign = $userData['role'] ?? null;
            unset($userData['role']); // Remove role from user data since it's not in fillable

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                    Storage::disk('public')->delete($user->avatar_url);
                }

                $avatar = $request->file('avatar');
                $avatarName = time() . '_' . Str::random(10) . '.' . $avatar->getClientOriginalExtension();
                $avatarPath = 'avatars/' . date('Y/m/d') . '/' . $avatarName;
                
                Storage::disk('public')->put($avatarPath, file_get_contents($avatar));
                $userData['avatar_url'] = $avatarPath;
            }

            // Update user
            $user->update($userData);

            // Update role if provided
            if ($roleToAssign) {
                $role = \Spatie\Permission\Models\Role::where('name', $roleToAssign)->first();
                if ($role) {
                    $user->syncRoles([$role]);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $user->load(['roles', 'permissions']),
                'message' => 'User updated successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a user
     */
    public function destroy($id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            // Prevent deleting the last admin
            if ($user->hasRole('Admin')) {
                $adminCount = User::role('Admin')->count();
                if ($adminCount <= 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete the last admin user'
                    ], 400);
                }
            }

            // Delete avatar if exists
            if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                Storage::disk('public')->delete($user->avatar_url);
            }

            // Revoke all tokens
            $user->tokens()->delete();

            // Delete user
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current user profile
     */
    public function profile(): JsonResponse
    {
        try {
            $user = auth()->user()->load(['roles', 'permissions']);

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Profile retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update current user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => [
                    'sometimes',
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id)
                ],
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userData = $validator->validated();
            unset($userData['avatar']); // Remove avatar from user data

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                    Storage::disk('public')->delete($user->avatar_url);
                }

                $avatar = $request->file('avatar');
                $avatarName = time() . '_' . Str::random(10) . '.' . $avatar->getClientOriginalExtension();
                $avatarPath = 'avatars/' . date('Y/m/d') . '/' . $avatarName;
                
                Storage::disk('public')->put($avatarPath, file_get_contents($avatar));
                $userData['avatar_url'] = $avatarPath;
            }

            // Update user
            $user->update($userData);

            return response()->json([
                'success' => true,
                'data' => $user->load(['roles', 'permissions']),
                'message' => 'Profile updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = auth()->user();

            // Check current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 400);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            // Revoke all tokens to force re-login
            $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully. Please login again.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset user password (Admin only)
     */
    public function resetPassword(Request $request, $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::findOrFail($id);

            // Update password
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            // Revoke all tokens
            $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'active_users' => User::whereNotNull('email_verified_at')->count(),
                'inactive_users' => User::whereNull('email_verified_at')->count(),
                'by_role' => [
                    ['role' => 'Admin', 'count' => User::role('Admin')->count()],
                    ['role' => 'Manager', 'count' => User::role('Manager')->count()],
                    ['role' => 'User', 'count' => User::role('User')->count()],
                ],
                'recent_registrations' => User::with('roles')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(['id', 'name', 'email', 'created_at']),
                'users_with_avatars' => User::whereNotNull('avatar_url')->count(),
                'users_without_avatars' => User::whereNull('avatar_url')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'User statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete users
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_ids' => 'required|array',
                'user_ids.*' => 'integer|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $deletedCount = 0;
            $failedDeletions = [];

            foreach ($request->user_ids as $userId) {
                try {
                    $user = User::findOrFail($userId);

                    // Prevent deleting the last admin
                    if ($user->hasRole('Admin')) {
                        $adminCount = User::role('Admin')->count();
                        if ($adminCount <= 1) {
                            $failedDeletions[] = [
                                'user_id' => $userId,
                                'error' => 'Cannot delete the last admin user'
                            ];
                            continue;
                        }
                    }

                    // Delete avatar if exists
                    if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                        Storage::disk('public')->delete($user->avatar_url);
                    }

                    // Revoke all tokens
                    $user->tokens()->delete();

                    // Delete user
                    $user->delete();
                    $deletedCount++;

                } catch (\Exception $e) {
                    $failedDeletions[] = [
                        'user_id' => $userId,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'deleted_count' => $deletedCount,
                    'failed_deletions' => $failedDeletions
                ],
                'message' => 'Bulk deletion completed. ' . $deletedCount . ' users deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk deletion: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available roles
     */
    public function getRoles(): JsonResponse
    {
        try {
            $roles = [
                'Admin' => 'Administrator - Full system access',
                'Manager' => 'Manager - Limited administrative access',
                'User' => 'User - Basic access'
            ];

            return response()->json([
                'success' => true,
                'data' => $roles,
                'message' => 'Roles retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve roles: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve user (Admin only)
     */
    public function approveUser($id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            // Mark user as approved by setting email_verified_at
            $user->update([
                'email_verified_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'data' => $user->load(['roles', 'permissions']),
                'message' => 'User approved successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject user (Admin only)
     */
    public function rejectUser($id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            // Mark user as rejected by setting email_verified_at to null
            $user->update([
                'email_verified_at' => null
            ]);

            return response()->json([
                'success' => true,
                'data' => $user->load(['roles', 'permissions']),
                'message' => 'User rejected successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk approve users (Admin only)
     */
    public function bulkApproveUsers(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_ids' => 'required|array',
                'user_ids.*' => 'integer|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $approvedCount = 0;
            $failedApprovals = [];

            foreach ($request->user_ids as $userId) {
                try {
                    $user = User::findOrFail($userId);
                    $user->update(['email_verified_at' => now()]);
                    $approvedCount++;
                } catch (\Exception $e) {
                    $failedApprovals[] = [
                        'user_id' => $userId,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'approved_count' => $approvedCount,
                    'failed_approvals' => $failedApprovals
                ],
                'message' => 'Bulk approval completed. ' . $approvedCount . ' users approved successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk approval: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user activity log
     */
    public function getActivityLog($id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $activities = $user->activities()->orderBy('created_at', 'desc')->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $activities,
                'message' => 'User activity log retrieved successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve activity log: ' . $e->getMessage()
            ], 500);
        }
    }
}
