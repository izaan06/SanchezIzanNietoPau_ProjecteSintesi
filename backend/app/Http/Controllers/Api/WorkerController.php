<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WorkerController extends Controller
{
    /**
     * Llistar tots els treballadors.
     */
    public function index(): JsonResponse
    {
        $workers = Worker::all();
        return response()->json($workers);
    }

    /**
     * Crear un nou treballador.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'cost_per_hour' => 'required|numeric|min:0',
            'availability' => 'boolean',
        ]);

        $worker = Worker::create($validated);

        return response()->json([
            'message' => 'Treballador creat correctament',
            'data' => $worker
        ], 201);
    }

    /**
     * Mostrar un treballador específic.
     */
    public function show(Worker $worker): JsonResponse
    {
        return response()->json($worker);
    }

    /**
     * Actualitzar dades d'un treballador.
     */
    public function update(Request $request, Worker $worker): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'role' => 'sometimes|required|string|max:255',
            'cost_per_hour' => 'sometimes|required|numeric|min:0',
            'availability' => 'boolean',
        ]);

        $worker->update($validated);

        return response()->json([
            'message' => 'Treballador actualitzat correctament',
            'data' => $worker
        ]);
    }

    /**
     * Eliminar un treballador.
     */
    public function destroy(Worker $worker): JsonResponse
    {
        $worker->delete();

        return response()->json([
            'message' => 'Treballador eliminat correctament'
        ]);
    }
}
