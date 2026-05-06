<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    /**
     * Llistar tots els esdeveniments (amb la info del client).
     */
    public function index(): JsonResponse
    {
        $events = Event::with(['client', 'appointmentRequest'])
            ->latest()
            ->get();
        return response()->json($events);
    }

    /**
     * Crear un nou esdeveniment.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'assistants' => 'nullable|integer|min:0',
            'status' => 'nullable|string|in:pending,confirmed,planning,in_progress,completed,cancelled',
            'client_id' => 'required|exists:clients,id',
            'tasks' => 'nullable|array',
        ]);

        $event = Event::create($validated);

        return response()->json([
            'message' => 'Esdeveniment creat correctament',
            'data' => $event->load(['client', 'appointmentRequest'])
        ], 201);
    }

    /**
     * Mostrar un esdeveniment específic.
     */
    public function show(Event $event): JsonResponse
    {
        return response()->json($event->load(['client', 'appointmentRequest']));
    }

    /**
     * Actualitzar dades d'un esdeveniment.
     */
    public function update(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'date' => 'sometimes|required|date',
            'location' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|max:255',
            'assistants' => 'nullable|integer|min:0',
            'status' => 'nullable|string|in:pending,confirmed,planning,in_progress,completed,cancelled',
            'client_id' => 'sometimes|required|exists:clients,id',
            'tasks' => 'nullable|array',
        ]);

        $event->update($validated);

        return response()->json([
            'message' => 'Esdeveniment actualitzat correctament',
            'data' => $event->load(['client', 'appointmentRequest'])
        ]);
    }

    /**
     * Eliminar un esdeveniment.
     */
    public function destroy(Event $event): JsonResponse
    {
        // Si té una sol·licitud vinculada, la eliminem també
        if ($event->appointment_request_id) {
            $event->appointmentRequest()->delete();
        }

        $event->delete();

        return response()->json([
            'message' => 'Esdeveniment i sol·licitud eliminats correctament'
        ]);
    }
}
