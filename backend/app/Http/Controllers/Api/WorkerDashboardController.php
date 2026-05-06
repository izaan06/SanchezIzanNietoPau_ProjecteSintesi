<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorkerDashboardController extends Controller
{
    /**
     * Retorna els events assignats al treballador autenticat.
     */
    public function myEvents(Request $request)
    {
        $user   = $request->user();
        $worker = $user->worker;

        if (! $worker) {
            return response()->json([
                'message' => 'No tens un perfil de treballador associat.',
                'events'  => [],
            ]);
        }

        $events = $worker->events()
            ->with(['workers' => function ($q) {
                $q->select('workers.id', 'workers.name', 'workers.role', 'workers.availability');
            }])
            ->orderBy('date')
            ->get()
            ->map(function ($event) use ($worker) {
                return [
                    'id'          => $event->id,
                    'name'        => $event->name,
                    'date'        => $event->date,
                    'status'      => $event->status,
                    'location'    => $event->location ?? null,
                    'attendees'   => $event->attendees ?? null,
                    'my_hours'    => $event->pivot->hours,
                    'my_notes'    => $event->pivot->notes,
                    'colleagues'  => $event->workers
                        ->where('id', '!=', $worker->id)
                        ->values(),
                ];
            });

        // Calcular disponibilitat real segons vacances aprovades
        $isOnLeave = $worker->timeOffs()
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->exists();

        return response()->json([
            'worker' => [
                'id'           => $worker->id,
                'name'         => $worker->name,
                'role'         => $worker->role,
                'cost_per_hour'=> $worker->cost_per_hour,
                'availability' => $worker->availability && !$isOnLeave,
                'is_on_leave'  => $isOnLeave
            ],
            'events' => $events,
        ]);
    }

    /**
     * Retorna tots els companys de treball (tots els workers).
     */
    public function myColleagues(Request $request)
    {
        $user   = $request->user();
        $worker = $user->worker;

        $colleagues = \App\Models\Worker::when($worker, fn($q) => $q->where('id', '!=', $worker->id))
            ->select('id', 'name', 'role', 'availability', 'cost_per_hour')
            ->get();

        return response()->json(['colleagues' => $colleagues]);
    }
}
