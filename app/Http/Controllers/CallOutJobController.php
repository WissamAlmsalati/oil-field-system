<?php

namespace App\Http\Controllers;

use App\Http\Requests\CallOutJobRequest;
use App\Models\CallOutJob;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CallOutJobController extends Controller
{
    /**
     * Display a listing of call-out jobs.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = CallOutJob::with(['client', 'client.contacts']);

            // Filter by client if provided
            if ($request->has('client_id')) {
                $query->where('client_id', $request->client_id);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('job_name', 'LIKE', "%{$search}%")
                      ->orWhere('work_order_number', 'LIKE', "%{$search}%")
                      ->orWhereHas('client', function ($clientQuery) use ($search) {
                          $clientQuery->where('name', 'LIKE', "%{$search}%");
                      });
                });
            }

            // Date range filtering
            if ($request->has('start_date_from')) {
                $query->where('start_date', '>=', $request->start_date_from);
            }
            if ($request->has('start_date_to')) {
                $query->where('start_date', '<=', $request->start_date_to);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $jobs = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $jobs,
                'message' => 'Call-out jobs retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving call-out jobs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created call-out job.
     */
    public function store(CallOutJobRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            
            // Set default status if not provided (must match DB enum)
            if (!isset($data['status'])) {
                $data['status'] = 'scheduled';
            }

            // Handle document uploads if present
            $documentPaths = [];
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('jobs', $filename, 'public');
                    $documentPaths[] = $path;
                }
                $data['documents'] = json_encode($documentPaths);
            }

            $job = CallOutJob::create($data);
            $job->load(['client', 'client.contacts']);

            return response()->json([
                'success' => true,
                'data' => $job,
                'message' => 'Call-out job created successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating call-out job: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified call-out job.
     */
    public function show($id): JsonResponse
    {
        try {
            $job = CallOutJob::with(['client', 'client.contacts'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $job,
                'message' => 'Call-out job retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Call-out job not found'
            ], 404);
        }
    }

    /**
     * Update the specified call-out job.
     */
    public function update(CallOutJobRequest $request, $id): JsonResponse
    {
        try {
            $job = CallOutJob::findOrFail($id);
            $data = $request->validated();

            // Handle document uploads if present
            if ($request->hasFile('documents')) {
                // Delete old documents if they exist
                if ($job->documents) {
                    $oldDocuments = json_decode($job->documents, true);
                    if (is_array($oldDocuments)) {
                        foreach ($oldDocuments as $oldDoc) {
                            Storage::disk('public')->delete($oldDoc);
                        }
                    }
                }

                // Upload new documents
                $documentPaths = [];
                foreach ($request->file('documents') as $file) {
                    $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('jobs', $filename, 'public');
                    $documentPaths[] = $path;
                }
                $data['documents'] = json_encode($documentPaths);
            }

            $job->update($data);
            $job->load(['client', 'client.contacts']);

            return response()->json([
                'success' => true,
                'data' => $job,
                'message' => 'Call-out job updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating call-out job: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified call-out job.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $job = CallOutJob::findOrFail($id);

            // Delete associated documents if they exist
            if ($job->documents) {
                $documents = json_decode($job->documents, true);
                if (is_array($documents)) {
                    foreach ($documents as $doc) {
                        Storage::disk('public')->delete($doc);
                    }
                }
            }

            $job->delete();

            return response()->json([
                'success' => true,
                'message' => 'Call-out job deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting call-out job: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get call-out jobs by client.
     */
    public function getByClient($clientId): JsonResponse
    {
        try {
            $client = Client::findOrFail($clientId);
            
            $jobs = CallOutJob::where('client_id', $clientId)
                ->with(['client', 'client.contacts'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'client' => $client,
                    'call_out_jobs' => $jobs
                ],
                'message' => 'Client call-out jobs retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving client call-out jobs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get call-out job statistics.
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total_jobs' => CallOutJob::count(),
                'pending_jobs' => CallOutJob::where('status', 'scheduled')->count(),
                'in_progress_jobs' => CallOutJob::where('status', 'in_progress')->count(),
                'completed_jobs' => CallOutJob::where('status', 'completed')->count(),
                'cancelled_jobs' => CallOutJob::where('status', 'cancelled')->count(),
                'jobs_this_month' => CallOutJob::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'overdue_jobs' => CallOutJob::where('end_date', '<', now())
                    ->whereIn('status', ['scheduled', 'in_progress'])
                    ->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Call-out job statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update job status.
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|string|in:scheduled,in_progress,completed,cancelled'
            ]);

            $job = CallOutJob::findOrFail($id);
            $job->update(['status' => $request->status]);
            
            $job->load(['client', 'client.contacts']);

            return response()->json([
                'success' => true,
                'data' => $job,
                'message' => 'Job status updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating job status: ' . $e->getMessage()
            ], 500);
        }
    }
}
