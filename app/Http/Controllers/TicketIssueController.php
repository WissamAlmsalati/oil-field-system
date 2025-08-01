<?php

namespace App\Http\Controllers;

use App\Models\TicketIssue;
use App\Models\ServiceTicket;
use App\Http\Requests\TicketIssueRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TicketIssueController extends Controller
{
    public function index(): JsonResponse
    {
        $issues = TicketIssue::with('ticket')->paginate(15);
        return response()->json([
            'success' => true,
            'data' => $issues,
            'message' => 'Ticket issues retrieved successfully'
        ]);
    }

    public function store(TicketIssueRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            
            // Create the ticket issue
            $ticketIssue = TicketIssue::create($validatedData);
            
            // Load the ticket relationship
            $ticketIssue->load('ticket');
            
            return response()->json([
                'success' => true,
                'data' => $ticketIssue,
                'message' => 'Ticket issue created successfully'
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket issue: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $ticketIssue = TicketIssue::with('ticket')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $ticketIssue,
                'message' => 'Ticket issue retrieved successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket issue not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve ticket issue: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(TicketIssueRequest $request, $id): JsonResponse
    {
        try {
            $ticketIssue = TicketIssue::findOrFail($id);
            $validatedData = $request->validated();
            
            // Update the ticket issue
            $ticketIssue->update($validatedData);
            
            // Load the ticket relationship
            $ticketIssue->load('ticket');
            
            return response()->json([
                'success' => true,
                'data' => $ticketIssue,
                'message' => 'Ticket issue updated successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket issue not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ticket issue: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $ticketIssue = TicketIssue::findOrFail($id);
            
            // Delete the ticket issue
            $ticketIssue->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Ticket issue deleted successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket issue not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete ticket issue: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getByTicket($ticketId): JsonResponse
    {
        try {
            // Check if ticket exists
            $ticket = ServiceTicket::find($ticketId);
            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service ticket not found'
                ], 404);
            }
            
            $issues = TicketIssue::where('ticket_id', $ticketId)
                ->with('ticket')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $issues,
                'message' => 'Ticket issues retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve ticket issues: ' . $e->getMessage()
            ], 500);
        }
    }
}
