<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public file download route for frontend
Route::get('/download/{filename}', function ($filename) {
    $filename = urldecode($filename);
    
    // Check if file exists in the daily_logs directory
    $filePath = 'daily_logs/excel/' . $filename;
    
    if (!Storage::disk('public')->exists($filePath)) {
        // Try PDF directory
        $filePath = 'daily_logs/pdf/' . $filename;
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File not found');
        }
    }
    
    $fullPath = Storage::disk('public')->path($filePath);
    
    // Determine content type based on file extension
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $contentType = $extension === 'xlsx' ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'application/pdf';
    
    // For frontend downloads, we'll serve the file directly
    return response()->file($fullPath, [
        'Content-Type' => $contentType,
        'Content-Disposition' => 'inline; filename="' . $filename . '"',
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
        'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0'
    ]);
})->name('download.file');

// Alternative download route that forces download
Route::get('/download-file/{filename}', function ($filename) {
    $filename = urldecode($filename);
    
    // Check if file exists in the daily_logs directory
    $filePath = 'daily_logs/excel/' . $filename;
    
    if (!Storage::disk('public')->exists($filePath)) {
        // Try PDF directory
        $filePath = 'daily_logs/pdf/' . $filename;
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File not found');
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
        'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0'
    ]);
})->name('download.file.force');
