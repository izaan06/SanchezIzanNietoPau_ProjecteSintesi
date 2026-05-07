<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EventWorkerController extends Controller
{
    /**
     * Assignar un treballador a un esdeveniment.
     */
    public function assignWorker(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'hours' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Assignem (o actualitzem si ja existeix) a la taula pivot
        $event->workers()->syncWithoutDetaching([
            $validated['worker_id'] => [
                'hours' => $validated['hours'] ?? 0,
                'notes' => $validated['notes'] ?? '',
            ]
        ]);

        return response()->json([
            'message' => 'Treballador assignat correctament a l\'esdeveniment',
            'data' => $event->load('workers')
        ]);
    }

    public function getWorkers(Event $event): JsonResponse
    {
        return response()->json([
            'data' => $event->workers
        ]);
    }

    /**
     * Desassignar un treballador d'un esdeveniment.
     */
    public function removeWorker(Event $event, Worker $worker): JsonResponse
    {
        $event->workers()->detach($worker->id);

        return response()->json([
            'message' => 'Treballador desassignat de l\'esdeveniment correctament',
            'data' => $event->load('workers')
        ]);
    }
}
