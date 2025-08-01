<?php

namespace App\Http\Controllers;

use App\Models\TicketIssue;
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

    public function store(Request $request): JsonResponse
    {
        // TODO: Add validation and full implementation
        return response()->json([
            'success' => false,
            'message' => 'Ticket issue creation not yet implemented'
        ], 501);
    }

    public function show($id): JsonResponse
    {
        // TODO: Add full implementation
        return response()->json([
            'success' => false,
            'message' => 'Ticket issue details not yet implemented'
        ], 501);
    }

    public function update(Request $request, $id): JsonResponse
    {
        // TODO: Add validation and full implementation
        return response()->json([
            'success' => false,
            'message' => 'Ticket issue update not yet implemented'
        ], 501);
    }

    public function destroy($id): JsonResponse
    {
        // TODO: Add full implementation
        return response()->json([
            'success' => false,
            'message' => 'Ticket issue deletion not yet implemented'
        ], 501);
    }

    public function getByTicket($ticketId): JsonResponse
    {
        $issues = TicketIssue::where('ticket_id', $ticketId)
            ->with('ticket')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $issues,
            'message' => 'Ticket issues retrieved successfully'
        ]);
    }
}
