<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware(['auth:sanctum', 'role:Admin']);
});

// Protected API Routes
Route::middleware(['auth:sanctum'])->group(function () {
    
    // Users Management (Admin only)
    Route::prefix('users')->middleware('role:Admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\UserController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\UserController::class, 'store']);
        Route::put('/{id}', [\App\Http\Controllers\UserController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\UserController::class, 'destroy']);
    });

    // Clients Management
    Route::prefix('clients')->group(function () {
        Route::get('/', [\App\Http\Controllers\ClientController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\ClientController::class, 'store']);
        Route::put('/{id}', [\App\Http\Controllers\ClientController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\ClientController::class, 'destroy']);
    });

    // Sub-Agreements Management
    Route::prefix('sub-agreements')->group(function () {
        Route::get('/', [\App\Http\Controllers\SubAgreementController::class, 'index']);
        Route::get('/stats', [\App\Http\Controllers\SubAgreementController::class, 'stats']);
        Route::get('/client/{clientId}', [\App\Http\Controllers\SubAgreementController::class, 'getByClient']);
        Route::get('/{id}', [\App\Http\Controllers\SubAgreementController::class, 'show']);
        Route::post('/', [\App\Http\Controllers\SubAgreementController::class, 'store']);
        Route::put('/{id}', [\App\Http\Controllers\SubAgreementController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\SubAgreementController::class, 'destroy']);
    });

    // Call-Out Jobs Management
    Route::prefix('call-out-jobs')->group(function () {
        Route::get('/', [\App\Http\Controllers\CallOutJobController::class, 'index']);
        Route::get('/stats', [\App\Http\Controllers\CallOutJobController::class, 'stats']);
        Route::get('/client/{clientId}', [\App\Http\Controllers\CallOutJobController::class, 'getByClient']);
        Route::get('/{id}', [\App\Http\Controllers\CallOutJobController::class, 'show']);
        Route::post('/', [\App\Http\Controllers\CallOutJobController::class, 'store']);
        Route::put('/{id}', [\App\Http\Controllers\CallOutJobController::class, 'update']);
        Route::patch('/{id}/status', [\App\Http\Controllers\CallOutJobController::class, 'updateStatus']);
        Route::delete('/{id}', [\App\Http\Controllers\CallOutJobController::class, 'destroy']);
    });

    // Daily Service Logs Management
    Route::prefix('daily-logs')->group(function () {
        Route::get('/', [\App\Http\Controllers\DailyServiceLogController::class, 'index']);
        Route::get('/{id}', [\App\Http\Controllers\DailyServiceLogController::class, 'show']);
        Route::get('/client/{clientId}', [\App\Http\Controllers\DailyServiceLogController::class, 'getByClient']);
        Route::post('/', [\App\Http\Controllers\DailyServiceLogController::class, 'store']);
        Route::put('/{id}', [\App\Http\Controllers\DailyServiceLogController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\DailyServiceLogController::class, 'destroy']);
        Route::post('/{id}/generate-excel', [\App\Http\Controllers\DailyServiceLogController::class, 'generateExcel']);
        Route::get('/{id}/download/{type}', [\App\Http\Controllers\DailyServiceLogController::class, 'downloadFile']);
        Route::get('/{id}/download-file/{type}', [\App\Http\Controllers\DailyServiceLogController::class, 'downloadFileDirect']);
    });

    // Public file download (no authentication required)
    Route::get('/daily-logs/public/download/{filename}', [\App\Http\Controllers\DailyServiceLogController::class, 'publicDownload']);

    // Service Tickets Management
    Route::prefix('service-tickets')->group(function () {
        Route::get('/', [\App\Http\Controllers\ServiceTicketController::class, 'index']);
        Route::get('/client/{clientId}', [\App\Http\Controllers\ServiceTicketController::class, 'getByClient']);
        Route::post('/', [\App\Http\Controllers\ServiceTicketController::class, 'store']);
        Route::put('/{id}', [\App\Http\Controllers\ServiceTicketController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\ServiceTicketController::class, 'destroy']);
        Route::post('/generate', [\App\Http\Controllers\ServiceTicketController::class, 'generateFromLogs']);
    });

    // Ticket Issues Management
    Route::prefix('ticket-issues')->group(function () {
        Route::get('/', [\App\Http\Controllers\TicketIssueController::class, 'index']);
        Route::get('/ticket/{ticketId}', [\App\Http\Controllers\TicketIssueController::class, 'getByTicket']);
        Route::post('/', [\App\Http\Controllers\TicketIssueController::class, 'store']);
        Route::put('/{id}', [\App\Http\Controllers\TicketIssueController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\TicketIssueController::class, 'destroy']);
    });

        // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [\App\Http\Controllers\DashboardController::class, 'stats']);
        Route::get('/recent-activities', [\App\Http\Controllers\DashboardController::class, 'recentActivities']);
    });

});
