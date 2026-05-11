<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Attendance;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class AttendanceController extends Controller
{
    /**
     * Llista tots els fitxatges (només admin).
     */
    public function index(): JsonResponse
    {
        $attendances = Attendance::with('worker')->latest('clock_in')->get();
        return response()->json(['data' => $attendances]);
    }

    /**
     * Obté l'estat actual del treballador (si ha fitxat o no).
     */
    public function status(Request $request): JsonResponse
    {
        $worker = Worker::where('user_id', $request->user()->id)->first();
        if (!$worker) {
            return response()->json(['error' => 'No s\'ha trobat el perfil de treballador'], 403);
        }

        $lastAttendance = Attendance::where('worker_id', $worker->id)
            ->whereNull('clock_out')
            ->latest()
            ->first();

        return response()->json([
            'is_clocked_in' => !!$lastAttendance,
            'last_session' => $lastAttendance,
            'worker' => $worker
        ]);
    }

    /**
     * Realitza el fitxatge d'entrada.
     */
    public function clockIn(Request $request): JsonResponse
    {
        $worker = Worker::where('user_id', $request->user()->id)->first();
        if (!$worker) {
            return response()->json(['error' => 'No ets un treballador registrat'], 403);
        }

        // Comprovar si ja té una sessió oberta
        $exists = Attendance::where('worker_id', $worker->id)->whereNull('clock_out')->exists();
        if ($exists) {
            return response()->json(['error' => 'Ja tens una sessió oberta'], 400);
        }

        $attendance = Attendance::create([
            'worker_id' => $worker->id,
            'clock_in' => Carbon::now('Europe/Madrid'),
            'location' => $request->location,
            'notes' => $request->notes
        ]);

        return response()->json([
            'message' => 'Entrada registrada amb èxit!',
            'data' => $attendance
        ]);
    }

    /**
     * Realitza el fitxatge de sortida.
     */
    public function clockOut(Request $request): JsonResponse
    {
        $worker = Worker::where('user_id', $request->user()->id)->first();
        if (!$worker) {
            return response()->json(['error' => 'No ets un treballador registrat'], 403);
        }

        $attendance = Attendance::where('worker_id', $worker->id)
            ->whereNull('clock_out')
            ->latest()
            ->first();

        if (!$attendance) {
            return response()->json(['error' => 'No tens cap sessió oberta per tancar'], 400);
        }

        $attendance->update([
            'clock_out' => Carbon::now('Europe/Madrid'),
            'notes' => $request->notes ?? $attendance->notes
        ]);

        return response()->json([
            'message' => 'Sortida registrada amb èxit. Bona feina!',
            'data' => $attendance
        ]);
    }
}
