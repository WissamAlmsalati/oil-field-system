<?php

namespace App\Http\Controllers;

use App\Models\DailyServiceLog;
use App\Models\Client;
use App\Http\Requests\DailyServiceLogRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DailyServiceLogController extends Controller
{
    public function index(): JsonResponse
    {
        $logs = DailyServiceLog::with('client')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return response()->json([
            'success' => true,
            'data' => $logs,
            'message' => 'Daily service logs retrieved successfully'
        ]);
    }

    public function store(DailyServiceLogRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            
            // Generate unique log number
            $validated['log_number'] = DailyServiceLog::generateLogNumber();
            
            // Handle file uploads if present
            if ($request->hasFile('excel_file')) {
                $excelFile = $request->file('excel_file');
                $excelFileName = 'excel_' . time() . '_' . $excelFile->getClientOriginalName();
                $excelPath = $excelFile->storeAs('daily_logs/excel', $excelFileName, 'public');
                $validated['excel_file_path'] = $excelPath;
                $validated['excel_file_name'] = $excelFileName;
            }
            
            if ($request->hasFile('pdf_file')) {
                $pdfFile = $request->file('pdf_file');
                $pdfFileName = 'pdf_' . time() . '_' . $pdfFile->getClientOriginalName();
                $pdfPath = $pdfFile->storeAs('daily_logs/pdf', $pdfFileName, 'public');
                $validated['pdf_file_path'] = $pdfPath;
                $validated['pdf_file_name'] = $pdfFileName;
            }
            
            $log = DailyServiceLog::create($validated);
            
            return response()->json([
                'success' => true,
                'data' => $log->load('client'),
                'message' => 'Daily service log created successfully'
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create daily service log: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $log = DailyServiceLog::with('client')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $log,
                'message' => 'Daily service log retrieved successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Daily service log not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve daily service log: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(DailyServiceLogRequest $request, $id): JsonResponse
    {
        try {
            $log = DailyServiceLog::findOrFail($id);
            $validated = $request->validated();
            
            // Handle file uploads if present
            if ($request->hasFile('excel_file')) {
                // Delete old file if exists
                if ($log->excel_file_path && Storage::disk('public')->exists($log->excel_file_path)) {
                    Storage::disk('public')->delete($log->excel_file_path);
                }
                
                $excelFile = $request->file('excel_file');
                $excelFileName = 'excel_' . time() . '_' . $excelFile->getClientOriginalName();
                $excelPath = $excelFile->storeAs('daily_logs/excel', $excelFileName, 'public');
                $validated['excel_file_path'] = $excelPath;
                $validated['excel_file_name'] = $excelFileName;
            }
            
            if ($request->hasFile('pdf_file')) {
                // Delete old file if exists
                if ($log->pdf_file_path && Storage::disk('public')->exists($log->pdf_file_path)) {
                    Storage::disk('public')->delete($log->pdf_file_path);
                }
                
                $pdfFile = $request->file('pdf_file');
                $pdfFileName = 'pdf_' . time() . '_' . $pdfFile->getClientOriginalName();
                $pdfPath = $pdfFile->storeAs('daily_logs/pdf', $pdfFileName, 'public');
                $validated['pdf_file_path'] = $pdfPath;
                $validated['pdf_file_name'] = $pdfFileName;
            }
            
            $log->update($validated);
            
            return response()->json([
                'success' => true,
                'data' => $log->load('client'),
                'message' => 'Daily service log updated successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Daily service log not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update daily service log: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $log = DailyServiceLog::findOrFail($id);
            
            // Delete associated files
            if ($log->excel_file_path && Storage::disk('public')->exists($log->excel_file_path)) {
                Storage::disk('public')->delete($log->excel_file_path);
            }
            
            if ($log->pdf_file_path && Storage::disk('public')->exists($log->pdf_file_path)) {
                Storage::disk('public')->delete($log->pdf_file_path);
            }
            
            $log->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Daily service log deleted successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Daily service log not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete daily service log: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getByClient($clientId): JsonResponse
    {
        try {
            // Verify client exists
            $client = Client::findOrFail($clientId);
            
            $logs = DailyServiceLog::where('client_id', $clientId)
                ->with('client')
                ->orderBy('date', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $logs,
                'message' => 'Client daily service logs retrieved successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Client not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve client daily service logs: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateExcel($id): JsonResponse
    {
        try {
            $log = DailyServiceLog::with('client')->findOrFail($id);
            
            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator('Almansoori Petroleum')
                ->setLastModifiedBy('Almansoori Petroleum')
                ->setTitle('Daily Service Log - ' . $log->log_number)
                ->setSubject('Daily Service Log Report')
                ->setDescription('Daily Service Log Report for ' . $log->log_number);
            
            // Set headers
            $sheet->setCellValue('A1', 'Daily Service Log');
            $sheet->setCellValue('A2', 'Log Number: ' . $log->log_number);
            $sheet->setCellValue('A3', 'Date: ' . $log->date->format('Y-m-d'));
            
            // Basic information
            $sheet->setCellValue('A5', 'Client:');
            $sheet->setCellValue('B5', $log->client->name);
            $sheet->setCellValue('A6', 'Field:');
            $sheet->setCellValue('B6', $log->field);
            $sheet->setCellValue('A7', 'Well:');
            $sheet->setCellValue('B7', $log->well);
            $sheet->setCellValue('A8', 'Contract:');
            $sheet->setCellValue('B8', $log->contract);
            $sheet->setCellValue('A9', 'Job No:');
            $sheet->setCellValue('B9', $log->job_no);
            
            if ($log->linked_job_id) {
                $sheet->setCellValue('A10', 'Linked Job ID:');
                $sheet->setCellValue('B10', $log->linked_job_id);
            }
            
            // Personnel section
            if ($log->personnel) {
                $sheet->setCellValue('A12', 'Personnel:');
                $sheet->setCellValue('A13', 'Name');
                $sheet->setCellValue('B13', 'Position');
                $sheet->setCellValue('C13', 'Hours');
                
                $row = 14;
                foreach ($log->personnel as $person) {
                    $sheet->setCellValue('A' . $row, $person['name'] ?? '');
                    $sheet->setCellValue('B' . $row, $person['position'] ?? '');
                    $sheet->setCellValue('C' . $row, $person['hours'] ?? '');
                    $row++;
                }
            }
            
            // Equipment section
            if ($log->equipment_used) {
                $startRow = $log->personnel ? $row + 2 : 12;
                $sheet->setCellValue('A' . $startRow, 'Equipment Used:');
                $sheet->setCellValue('A' . ($startRow + 1), 'Name');
                $sheet->setCellValue('B' . ($startRow + 1), 'Hours');
                
                $row = $startRow + 2;
                foreach ($log->equipment_used as $equipment) {
                    $sheet->setCellValue('A' . $row, $equipment['name'] ?? '');
                    $sheet->setCellValue('B' . $row, $equipment['hours'] ?? '');
                    $row++;
                }
            }
            
            // Almansoori Representatives
            if ($log->almansoori_rep) {
                $startRow = $row + 2;
                $sheet->setCellValue('A' . $startRow, 'Almansoori Representatives:');
                $sheet->setCellValue('A' . ($startRow + 1), 'Name');
                $sheet->setCellValue('B' . ($startRow + 1), 'Position');
                
                $row = $startRow + 2;
                foreach ($log->almansoori_rep as $rep) {
                    $sheet->setCellValue('A' . $row, $rep['name'] ?? '');
                    $sheet->setCellValue('B' . $row, $rep['position'] ?? '');
                    $row++;
                }
            }
            
            // Approvals
            if ($log->mog_approval_1 || $log->mog_approval_2) {
                $startRow = $row + 2;
                $sheet->setCellValue('A' . $startRow, 'Approvals:');
                
                if ($log->mog_approval_1) {
                    $sheet->setCellValue('A' . ($startRow + 1), 'MOG Approval 1:');
                    $sheet->setCellValue('B' . ($startRow + 1), $log->mog_approval_1['name'] ?? '');
                    $sheet->setCellValue('C' . ($startRow + 1), $log->mog_approval_1['date'] ?? '');
                }
                
                if ($log->mog_approval_2) {
                    $sheet->setCellValue('A' . ($startRow + 2), 'MOG Approval 2:');
                    $sheet->setCellValue('B' . ($startRow + 2), $log->mog_approval_2['name'] ?? '');
                    $sheet->setCellValue('C' . ($startRow + 2), $log->mog_approval_2['date'] ?? '');
                }
            }
            
            // Auto-size columns
            foreach (range('A', 'C') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Create filename
            $filename = 'daily_service_log_' . $log->log_number . '_' . date('Y-m-d_H-i-s') . '.xlsx';
            $filepath = 'daily_logs/excel/' . $filename;
            
            // Save to storage
            $writer = new Xlsx($spreadsheet);
            $writer->save(storage_path('app/public/' . $filepath));
            
            // Update log with new file info
            $log->update([
                'excel_file_path' => $filepath,
                'excel_file_name' => $filename
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'file_path' => $filepath,
                    'file_name' => $filename,
                    'download_url' => url('storage/' . $filepath),
                    'public_download_url' => url('download/' . urlencode($filename)),
                    'force_download_url' => url('download-file/' . urlencode($filename))
                ],
                'message' => 'Excel file generated successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Daily service log not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate Excel file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadFile($id, $type): JsonResponse
    {
        try {
            $log = DailyServiceLog::findOrFail($id);
            
            $filePath = null;
            $fileName = null;
            
            if ($type === 'excel' && $log->excel_file_path) {
                $filePath = $log->excel_file_path;
                $fileName = $log->excel_file_name;
            } elseif ($type === 'pdf' && $log->pdf_file_path) {
                $filePath = $log->pdf_file_path;
                $fileName = $log->pdf_file_name;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }
            
            if (!Storage::disk('public')->exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found on server'
                ], 404);
            }
            
            $url = url('storage/' . $filePath);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'download_url' => $url,
                    'file_name' => $fileName
                ],
                'message' => 'File download URL generated successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Daily service log not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate download URL: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadFileDirect($id, $type)
    {
        try {
            $log = DailyServiceLog::findOrFail($id);
            
            $filePath = null;
            $fileName = null;
            
            if ($type === 'excel' && $log->excel_file_path) {
                $filePath = $log->excel_file_path;
                $fileName = $log->excel_file_name;
            } elseif ($type === 'pdf' && $log->pdf_file_path) {
                $filePath = $log->pdf_file_path;
                $fileName = $log->pdf_file_name;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }
            
            if (!Storage::disk('public')->exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found on server'
                ], 404);
            }
            
            $fullPath = Storage::disk('public')->path($filePath);
            
            return response()->download($fullPath, $fileName, [
                'Content-Type' => $type === 'excel' ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Daily service log not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function publicDownload($filename)
    {
        try {
            // Decode the filename if it's URL encoded
            $filename = urldecode($filename);
            
            // Check if file exists in the daily_logs directory
            $filePath = 'daily_logs/excel/' . $filename;
            
            if (!Storage::disk('public')->exists($filePath)) {
                // Try PDF directory
                $filePath = 'daily_logs/pdf/' . $filename;
                if (!Storage::disk('public')->exists($filePath)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File not found'
                    ], 404);
                }
            }
            
            $fullPath = Storage::disk('public')->path($filePath);
            
            // Determine content type based on file extension
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $contentType = $extension === 'xlsx' ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'application/pdf';
            
            return response()->download($fullPath, $filename, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Authorization'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download file: ' . $e->getMessage()
            ], 500);
        }
    }
}
