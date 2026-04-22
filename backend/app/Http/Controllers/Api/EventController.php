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
        $events = Event::with('client')->get();
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
            'status' => 'nullable|string|in:pendent,realitzat,cancel·lat',
            'client_id' => 'required|exists:clients,id',
        ]);

        $event = Event::create($validated);

        return response()->json([
            'message' => 'Esdeveniment creat correctament',
            'data' => $event->load('client')
        ], 201);
    }

    /**
     * Mostrar un esdeveniment específic.
     */
    public function show(Event $event): JsonResponse
    {
        return response()->json($event->load('client'));
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
            'status' => 'nullable|string|in:pendent,realitzat,cancel·lat',
            'client_id' => 'sometimes|required|exists:clients,id',
        ]);

        $event->update($validated);

        return response()->json([
            'message' => 'Esdeveniment actualitzat correctament',
            'data' => $event->load('client')
        ]);
    }

    /**
     * Eliminar un esdeveniment.
     */
    public function destroy(Event $event): JsonResponse
    {
        $event->delete();

        return response()->json([
            'message' => 'Esdeveniment eliminat correctament'
        ]);
    }
}
