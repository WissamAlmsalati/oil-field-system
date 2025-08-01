<?php

namespace App\Http\Controllers;

use App\Models\ServiceTicket;
use App\Models\Client;
use App\Models\SubAgreement;
use App\Models\CallOutJob;
use App\Http\Requests\ServiceTicketRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceTicketController extends Controller
{
    public function index(): JsonResponse
    {
        $tickets = ServiceTicket::with(['client', 'subAgreement'])->paginate(15);
        return response()->json([
            'success' => true,
            'data' => $tickets,
            'message' => 'Service tickets retrieved successfully'
        ]);
    }

    public function store(ServiceTicketRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            
            // Generate unique ticket number
            $validatedData['ticket_number'] = ServiceTicket::generateTicketNumber();
            
            // Create the service ticket
            $serviceTicket = ServiceTicket::create($validatedData);
            
            // Load relationships
            $serviceTicket->load(['client', 'subAgreement', 'callOutJob']);
            
            return response()->json([
                'success' => true,
                'data' => $serviceTicket,
                'message' => 'Service ticket created successfully'
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create service ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $serviceTicket = ServiceTicket::with([
                'client', 
                'subAgreement', 
                'callOutJob', 
                'ticketIssues'
            ])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $serviceTicket,
                'message' => 'Service ticket retrieved successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Service ticket not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve service ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(ServiceTicketRequest $request, $id): JsonResponse
    {
        try {
            $serviceTicket = ServiceTicket::findOrFail($id);
            $validatedData = $request->validated();
            
            // Update the service ticket
            $serviceTicket->update($validatedData);
            
            // Load relationships
            $serviceTicket->load(['client', 'subAgreement', 'callOutJob', 'ticketIssues']);
            
            return response()->json([
                'success' => true,
                'data' => $serviceTicket,
                'message' => 'Service ticket updated successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Service ticket not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $serviceTicket = ServiceTicket::findOrFail($id);
            
            // Delete the service ticket
            $serviceTicket->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Service ticket deleted successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Service ticket not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getByClient($clientId): JsonResponse
    {
        try {
            // Check if client exists
            $client = Client::find($clientId);
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found'
                ], 404);
            }
            
            $tickets = ServiceTicket::where('client_id', $clientId)
                ->with(['client', 'subAgreement', 'callOutJob'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $tickets,
                'message' => 'Client service tickets retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve client service tickets: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateFromLogs(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'client_id' => 'required|exists:clients,id',
                'log_ids' => 'required|array|min:1',
                'log_ids.*' => 'integer|exists:daily_service_logs,id',
                'sub_agreement_id' => 'nullable|exists:sub_agreements,id',
                'call_out_job_id' => 'nullable|exists:call_out_jobs,id',
                'date' => 'required|date',
                'amount' => 'required|numeric|min:0',
                'status' => 'required|in:In Field to Sign,Issue,Delivered,Invoiced'
            ]);
            
            // Generate unique ticket number
            $validatedData['ticket_number'] = ServiceTicket::generateTicketNumber();
            
            // Create the service ticket
            $serviceTicket = ServiceTicket::create($validatedData);
            
            // Load relationships
            $serviceTicket->load(['client', 'subAgreement', 'callOutJob']);
            
            return response()->json([
                'success' => true,
                'data' => $serviceTicket,
                'message' => 'Service ticket generated from logs successfully'
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate service ticket: ' . $e->getMessage()
            ], 500);
        }
    }
}
