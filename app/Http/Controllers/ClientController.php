<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;

class ClientController extends Controller
{
    /**
     * Display a listing of clients.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $clients = Client::with('contacts')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $clients->items(),
                'message' => 'Clients retrieved successfully',
                'pagination' => [
                    'page' => $clients->currentPage(),
                    'limit' => $clients->perPage(),
                    'total' => $clients->total(),
                    'totalPages' => $clients->lastPage()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'FETCH_ERROR',
                    'message' => 'Failed to retrieve clients'
                ]
            ], 500);
        }
    }

    /**
     * Store a newly created client.
     */
    public function store(StoreClientRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Handle logo upload if provided
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('logos', 'public');
                $data['logo_file_path'] = $logoPath;
                $data['logo_url'] = Storage::url($logoPath);
            }

            $client = Client::create($data);

            // Add contact people if provided
            if ($request->has('contacts')) {
                foreach ($request->contacts as $contact) {
                    $client->contacts()->create($contact);
                }
            }

            $client->load('contacts');

            return response()->json([
                'success' => true,
                'data' => $client,
                'message' => 'Client created successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'CREATE_ERROR',
                    'message' => 'Failed to create client'
                ]
            ], 500);
        }
    }

    /**
     * Update the specified client.
     */
    public function update(UpdateClientRequest $request, $id): JsonResponse
    {
        try {
            $client = Client::findOrFail($id);
            $data = $request->validated();

            // Handle logo upload if provided
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($client->logo_file_path) {
                    Storage::disk('public')->delete($client->logo_file_path);
                }

                $logoPath = $request->file('logo')->store('logos', 'public');
                $data['logo_file_path'] = $logoPath;
                $data['logo_url'] = Storage::url($logoPath);
            }

            $client->update($data);

            // Update contact people if provided
            if ($request->has('contacts')) {
                $client->contacts()->delete();
                foreach ($request->contacts as $contact) {
                    $client->contacts()->create($contact);
                }
            }

            $client->load('contacts');

            return response()->json([
                'success' => true,
                'data' => $client,
                'message' => 'Client updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UPDATE_ERROR',
                    'message' => 'Failed to update client'
                ]
            ], 500);
        }
    }

    /**
     * Remove the specified client.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $client = Client::findOrFail($id);

            // Delete logo file if exists
            if ($client->logo_file_path) {
                Storage::disk('public')->delete($client->logo_file_path);
            }

            $client->delete();

            return response()->json([
                'success' => true,
                'message' => 'Client deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'DELETE_ERROR',
                    'message' => 'Failed to delete client'
                ]
            ], 500);
        }
    }
}
