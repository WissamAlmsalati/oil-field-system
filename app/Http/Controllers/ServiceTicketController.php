<?php

namespace App\Http\Controllers;

use App\Models\ServiceTicket;
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

    public function store(Request $request): JsonResponse
    {
        // TODO: Add validation and full implementation
        return response()->json([
            'success' => false,
            'message' => 'Service ticket creation not yet implemented'
        ], 501);
    }

    public function show($id): JsonResponse
    {
        // TODO: Add full implementation
        return response()->json([
            'success' => false,
            'message' => 'Service ticket details not yet implemented'
        ], 501);
    }

    public function update(Request $request, $id): JsonResponse
    {
        // TODO: Add validation and full implementation
        return response()->json([
            'success' => false,
            'message' => 'Service ticket update not yet implemented'
        ], 501);
    }

    public function destroy($id): JsonResponse
    {
        // TODO: Add full implementation
        return response()->json([
            'success' => false,
            'message' => 'Service ticket deletion not yet implemented'
        ], 501);
    }

    public function getByClient($clientId): JsonResponse
    {
        $tickets = ServiceTicket::where('client_id', $clientId)
            ->with(['client', 'subAgreement'])
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $tickets,
            'message' => 'Client service tickets retrieved successfully'
        ]);
    }
}
