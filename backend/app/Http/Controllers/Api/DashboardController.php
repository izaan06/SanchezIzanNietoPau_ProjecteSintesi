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

        // 2. Esdeveniments per estat
        $eventsByStatus = Event::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // 3. Cost estimat total (Sumatori de hores * preu/hora de cada treballador assignat)
        // Utilitzem un join entre la taula pivot i els treballadors per obtenir el cost
        $totalEstimatedCost = DB::table('event_worker')
            ->join('workers', 'event_worker.worker_id', '=', 'workers.id')
            ->sum(DB::raw('event_worker.hours * workers.cost_per_hour'));

        return response()->json([
            'total_events' => $totalEvents,
            'events_by_status' => $eventsByStatus,
            'total_estimated_cost' => round($totalEstimatedCost, 2),
            'message' => 'Estadístiques del dashboard carregades correctament'
        ]);
    }
}
