<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Client;
use App\Http\Requests\DocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Document::with(['client', 'uploadedBy']);

            // Apply filters
            if ($request->has('category')) {
                $query->byCategory($request->category);
            }

            if ($request->has('client_id')) {
                $query->byClient($request->client_id);
            }

            if ($request->has('file_type')) {
                $query->byFileType($request->file_type);
            }

            if ($request->has('tags')) {
                $query->byTags($request->tags);
            }

            if ($request->has('search')) {
                $query->search($request->search);
            }

            if ($request->has('public_only') && $request->public_only) {
                $query->public();
            }

            // Filter by expiry date
            if ($request->has('expired_only') && $request->expired_only) {
                $query->where('expiry_date', '<', now());
            }

            if ($request->has('not_expired') && $request->not_expired) {
                $query->where(function($q) {
                    $q->where('expiry_date', '>', now())
                      ->orWhereNull('expiry_date');
                });
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $documents = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $documents,
                'message' => 'Documents retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve documents: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(DocumentRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            
            // Handle file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $filePath = 'documents/' . date('Y/m/d') . '/' . $fileName;
                
                // Store the file
                Storage::disk('public')->put($filePath, file_get_contents($file));
                
                // Set file information
                $validatedData['file_name'] = $file->getClientOriginalName();
                $validatedData['file_path'] = $filePath;
                $validatedData['file_size'] = $file->getSize();
                $validatedData['file_type'] = $file->getClientOriginalExtension();
                $validatedData['mime_type'] = $file->getMimeType();
            }
            
            // Set uploaded by user
            $validatedData['uploaded_by'] = auth()->id();
            
            // Create the document
            $document = Document::create($validatedData);
            
            // Load relationships
            $document->load(['client', 'uploadedBy']);
            
            return response()->json([
                'success' => true,
                'data' => $document,
                'message' => 'Document uploaded successfully'
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload document: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkStore(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'files.*' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,gif,zip,rar',
                'category' => 'required|in:Contract,Invoice,Report,Certificate,License,Manual,Procedure,Policy,Form,Other',
                'client_id' => 'nullable|exists:clients,id',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:50',
                'is_public' => 'nullable|boolean',
            ]);

            $uploadedDocuments = [];
            $failedUploads = [];

            DB::beginTransaction();

            foreach ($request->file('files') as $file) {
                try {
                    $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $filePath = 'documents/' . date('Y/m/d') . '/' . $fileName;
                    
                    // Store the file
                    Storage::disk('public')->put($filePath, file_get_contents($file));
                    
                    // Create document record
                    $document = Document::create([
                        'title' => $file->getClientOriginalName(),
                        'description' => 'Bulk uploaded document',
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_size' => $file->getSize(),
                        'file_type' => $file->getClientOriginalExtension(),
                        'mime_type' => $file->getMimeType(),
                        'category' => $request->category,
                        'tags' => $request->tags ?? [],
                        'client_id' => $request->client_id,
                        'uploaded_by' => auth()->id(),
                        'is_public' => $request->is_public ?? false,
                    ]);

                    $uploadedDocuments[] = $document;
                } catch (\Exception $e) {
                    $failedUploads[] = [
                        'file' => $file->getClientOriginalName(),
                        'error' => $e->getMessage()
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'uploaded' => $uploadedDocuments,
                    'failed' => $failedUploads
                ],
                'message' => 'Bulk upload completed. ' . count($uploadedDocuments) . ' documents uploaded successfully.'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk upload: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $document = Document::with(['client', 'uploadedBy'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $document,
                'message' => 'Document retrieved successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve document: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(DocumentRequest $request, $id): JsonResponse
    {
        try {
            $document = Document::findOrFail($id);
            $validatedData = $request->validated();
            
            // Handle file replacement if new file is uploaded
            if ($request->hasFile('file')) {
                // Delete old file
                if (Storage::disk('public')->exists($document->file_path)) {
                    Storage::disk('public')->delete($document->file_path);
                }
                
                $file = $request->file('file');
                $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $filePath = 'documents/' . date('Y/m/d') . '/' . $fileName;
                
                // Store the new file
                Storage::disk('public')->put($filePath, file_get_contents($file));
                
                // Update file information
                $validatedData['file_name'] = $file->getClientOriginalName();
                $validatedData['file_path'] = $filePath;
                $validatedData['file_size'] = $file->getSize();
                $validatedData['file_type'] = $file->getClientOriginalExtension();
                $validatedData['mime_type'] = $file->getMimeType();
            }
            
            // Update the document
            $document->update($validatedData);
            
            // Load relationships
            $document->load(['client', 'uploadedBy']);
            
            return response()->json([
                'success' => true,
                'data' => $document,
                'message' => 'Document updated successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update document: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $document = Document::findOrFail($id);
            
            // Delete the file from storage
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            
            // Delete the document record
            $document->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete document: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'document_ids' => 'required|array',
                'document_ids.*' => 'integer|exists:documents,id'
            ]);

            $deletedCount = 0;
            $failedDeletions = [];

            DB::beginTransaction();

            foreach ($request->document_ids as $documentId) {
                try {
                    $document = Document::findOrFail($documentId);
                    
                    // Delete the file from storage
                    if (Storage::disk('public')->exists($document->file_path)) {
                        Storage::disk('public')->delete($document->file_path);
                    }
                    
                    // Delete the document record
                    $document->delete();
                    $deletedCount++;
                } catch (\Exception $e) {
                    $failedDeletions[] = [
                        'document_id' => $documentId,
                        'error' => $e->getMessage()
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'deleted_count' => $deletedCount,
                    'failed_deletions' => $failedDeletions
                ],
                'message' => 'Bulk deletion completed. ' . $deletedCount . ' documents deleted successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk deletion: ' . $e->getMessage()
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
            
            $documents = Document::where('client_id', $clientId)
                ->with(['client', 'uploadedBy'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $documents,
                'message' => 'Client documents retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve client documents: ' . $e->getMessage()
            ], 500);
        }
    }

    public function download($id): JsonResponse
    {
        try {
            $document = Document::findOrFail($id);
            
            // Check if file exists
            if (!Storage::disk('public')->exists($document->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }
            
            // Increment download count
            $document->incrementDownloadCount();
            
            // Generate download URL
            $downloadUrl = url('storage/' . $document->file_path);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'download_url' => $downloadUrl,
                    'file_name' => $document->file_name,
                    'file_size' => $document->file_size_human,
                    'mime_type' => $document->mime_type
                ],
                'message' => 'Download URL generated successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate download URL: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadDirect($id)
    {
        try {
            $document = Document::findOrFail($id);
            
            // Check if file exists
            if (!Storage::disk('public')->exists($document->file_path)) {
                abort(404, 'File not found');
            }
            
            // Increment download count
            $document->incrementDownloadCount();
            
            $fullPath = Storage::disk('public')->path($document->file_path);
            
            return response()->download($fullPath, $document->file_name, [
                'Content-Type' => $document->mime_type,
                'Content-Disposition' => 'attachment; filename="' . $document->file_name . '"',
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Document not found');
        } catch (\Exception $e) {
            abort(500, 'Failed to download file');
        }
    }

    public function publicDownload($filename)
    {
        try {
            // Find document by filename
            $document = Document::where('file_name', $filename)
                ->where('is_public', true)
                ->first();
            
            if (!$document) {
                abort(404, 'Document not found or not public');
            }
            
            // Check if file exists
            if (!Storage::disk('public')->exists($document->file_path)) {
                abort(404, 'File not found');
            }
            
            // Increment download count
            $document->incrementDownloadCount();
            
            $fullPath = Storage::disk('public')->path($document->file_path);
            
            return response()->download($fullPath, $document->file_name, [
                'Content-Type' => $document->mime_type,
                'Content-Disposition' => 'attachment; filename="' . $document->file_name . '"'
            ]);
            
        } catch (\Exception $e) {
            abort(500, 'Failed to download file');
        }
    }

    public function preview($id)
    {
        try {
            $document = Document::findOrFail($id);
            
            // Check if file exists
            if (!Storage::disk('public')->exists($document->file_path)) {
                abort(404, 'File not found');
            }
            
            // Check if file type is previewable
            $previewableTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt'];
            if (!in_array(strtolower($document->file_type), $previewableTypes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File type not supported for preview'
                ], 400);
            }
            
            $fullPath = Storage::disk('public')->path($document->file_path);
            
            return response()->file($fullPath, [
                'Content-Type' => $document->mime_type,
                'Content-Disposition' => 'inline; filename="' . $document->file_name . '"'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Document not found');
        } catch (\Exception $e) {
            abort(500, 'Failed to preview file');
        }
    }

    public function getCategories(): JsonResponse
    {
        try {
            $categories = [
                'Contract' => 'Contracts and Agreements',
                'Invoice' => 'Invoices and Billing',
                'Report' => 'Reports and Analytics',
                'Certificate' => 'Certificates and Licenses',
                'License' => 'Licenses and Permits',
                'Manual' => 'Manuals and Guides',
                'Procedure' => 'Procedures and Policies',
                'Policy' => 'Policies and Standards',
                'Form' => 'Forms and Templates',
                'Other' => 'Other Documents'
            ];
            
            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Document categories retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStats(): JsonResponse
    {
        try {
            $stats = [
                'total_documents' => Document::count(),
                'total_size' => Document::sum('file_size'),
                'total_downloads' => Document::sum('download_count'),
                'by_category' => Document::selectRaw('category, COUNT(*) as count')
                    ->groupBy('category')
                    ->get(),
                'by_file_type' => Document::selectRaw('file_type, COUNT(*) as count')
                    ->groupBy('file_type')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get(),
                'recent_uploads' => Document::with(['client', 'uploadedBy'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(),
                'expired_documents' => Document::where('expiry_date', '<', now())->count(),
                'public_documents' => Document::where('is_public', true)->count(),
                'storage_usage' => [
                    'total_size_human' => $this->formatBytes(Document::sum('file_size')),
                    'average_file_size' => $this->formatBytes(Document::avg('file_size'))
                ]
            ];
            
            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Document statistics retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
} 