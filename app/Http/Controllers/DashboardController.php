<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\SubAgreement;
use App\Models\CallOutJob;
use App\Models\ServiceTicket;
use App\Models\DailyServiceLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'clients_count' => Client::count(),
                'sub_agreements_count' => SubAgreement::count(),
                'call_out_jobs_count' => CallOutJob::count(),
                'service_tickets_count' => ServiceTicket::count(),
                'daily_logs_count' => DailyServiceLog::count(),
                'total_agreements_amount' => SubAgreement::sum('amount'),
                'total_agreements_balance' => SubAgreement::sum('balance'),
                'active_agreements' => SubAgreement::where('end_date', '>=', now())->count(),
                'expired_agreements' => SubAgreement::where('end_date', '<', now())->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Dashboard statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving dashboard statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    public function recentActivities(): JsonResponse
    {
        try {
            $activities = Activity::with('causer')
                ->latest()
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $activities,
                'message' => 'Recent activities retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving recent activities: ' . $e->getMessage()
            ], 500);
        }
    }
}
