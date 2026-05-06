<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Retorna les estadístiques del dashboard.
     */
    public function index(): JsonResponse
    {
        // 1. Total d'esdeveniments
        $totalEvents = Event::count();

        // 2. Clients i Treballadors totals
        $totalClients = \App\Models\Client::count();
        $totalWorkers = \App\Models\Worker::count();

        // 3. Finances Reals (vinculades a AppointmentRequests)
        $financials = DB::table('events')
            ->join('appointment_requests', 'events.appointment_request_id', '=', 'appointment_requests.id')
            ->select(
                DB::raw('SUM(appointment_requests.ai_estimated_budget) as total_revenue'),
                DB::raw('SUM(appointment_requests.ai_estimated_profit) as total_profit'),
                DB::raw('SUM(appointment_requests.ai_operational_cost) as total_operational_cost')
            )
            ->first();

        // 4. Pròxims esdeveniments (els 5 més recents o futurs)
        $recentEvents = Event::with('client')
            ->orderBy('date', 'asc')
            ->where('date', '>=', now())
            ->limit(5)
            ->get();

        // 5. Estadístiques per estat
        $eventsByStatus = Event::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        return response()->json([
            'total_events' => $totalEvents,
            'total_clients' => $totalClients,
            'total_workers' => $totalWorkers,
            'total_revenue' => round($financials->total_revenue ?? 0, 2),
            'total_profit' => round($financials->total_profit ?? 0, 2),
            'total_operational_cost' => round($financials->total_operational_cost ?? 0, 2),
            'recent_events' => $recentEvents,
            'events_by_status' => $eventsByStatus,
        ]);
    }
}
