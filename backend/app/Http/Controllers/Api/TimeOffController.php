<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimeOff;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TimeOffController extends Controller
{
    /**
     * Llistar peticions de vacances (Admin veu totes, Worker només les seves).
     */
    public function index(Request $request): JsonResponse
    {
        $query = TimeOff::with('worker');

        if ($request->user()->isWorker()) {
            $query->where('worker_id', $request->user()->worker->id);
        }

        return response()->json($query->latest()->get());
    }

    /**
     * Crear una nova petició (només Worker).
     */
    public function store(Request $request): JsonResponse
    {
        $worker = $request->user()->worker;
        if (!$worker) {
            return response()->json(['message' => 'No ets un treballador registrat'], 403);
        }

        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'nullable|string|max:500',
        ]);

        $validated['worker_id'] = $worker->id;
        $validated['status'] = 'pending';

        $timeOff = TimeOff::create($validated);

        return response()->json([
            'message' => 'Petició enviada correctament',
            'data'    => $timeOff
        ], 201);
    }

    /**
     * Aprovar o rebutjar petició (només Admin).
     */
    public function updateStatus(Request $request, TimeOff $timeOff): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $timeOff->update(['status' => $request->status]);

        return response()->json([
            'message' => "Petició {$request->status} correctament",
            'data'    => $timeOff
        ]);
    }
}
