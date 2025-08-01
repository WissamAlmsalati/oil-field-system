<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubAgreementRequest;
use App\Models\SubAgreement;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class SubAgreementController extends Controller
{
    /**
     * Display a listing of sub-agreements.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = SubAgreement::with(['client', 'client.contacts']);

            // Filter by client if provided
            if ($request->has('client_id')) {
                $query->where('client_id', $request->client_id);
            }

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhereHas('client', function ($clientQuery) use ($search) {
                          $clientQuery->where('name', 'LIKE', "%{$search}%");
                      });
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $subAgreements = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $subAgreements,
                'message' => 'Sub-agreements retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving sub-agreements: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sub-agreements by client.
     */
    public function getByClient($clientId): JsonResponse
    {
        try {
            $client = Client::findOrFail($clientId);
            
            $subAgreements = SubAgreement::where('client_id', $clientId)
                ->with(['client', 'client.contacts'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'client' => $client,
                    'sub_agreements' => $subAgreements
                ],
                'message' => 'Client sub-agreements retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving client sub-agreements: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created sub-agreement.
     */
    public function store(SubAgreementRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Handle document upload if present
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('agreements', $filename, 'public');
                $data['document_path'] = $path;
            }

            $subAgreement = SubAgreement::create($data);
            $subAgreement->load(['client', 'client.contacts']);

            return response()->json([
                'success' => true,
                'data' => $subAgreement,
                'message' => 'Sub-agreement created successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating sub-agreement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified sub-agreement.
     */
    public function show($id): JsonResponse
    {
        try {
            $subAgreement = SubAgreement::with(['client', 'client.contacts'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $subAgreement,
                'message' => 'Sub-agreement retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sub-agreement not found'
            ], 404);
        }
    }

    /**
     * Update the specified sub-agreement.
     */
    public function update(SubAgreementRequest $request, $id): JsonResponse
    {
        try {
            $subAgreement = SubAgreement::findOrFail($id);
            $data = $request->validated();

            // Handle document upload if present
            if ($request->hasFile('document')) {
                // Delete old document if exists
                if ($subAgreement->document_path) {
                    Storage::disk('public')->delete($subAgreement->document_path);
                }

                $file = $request->file('document');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('agreements', $filename, 'public');
                $data['document_path'] = $path;
            }

            $subAgreement->update($data);
            $subAgreement->load(['client', 'client.contacts']);

            return response()->json([
                'success' => true,
                'data' => $subAgreement,
                'message' => 'Sub-agreement updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating sub-agreement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified sub-agreement.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $subAgreement = SubAgreement::findOrFail($id);

            // Delete associated document if exists
            if ($subAgreement->document_path) {
                Storage::disk('public')->delete($subAgreement->document_path);
            }

            $subAgreement->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sub-agreement deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting sub-agreement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sub-agreement statistics.
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total_agreements' => SubAgreement::count(),
                'total_amount' => SubAgreement::sum('amount'),
                'total_balance' => SubAgreement::sum('balance'),
                'active_agreements' => SubAgreement::where('end_date', '>=', now())->count(),
                'expired_agreements' => SubAgreement::where('end_date', '<', now())->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Sub-agreement statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}
