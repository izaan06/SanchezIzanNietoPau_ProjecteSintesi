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
     * Crear un nou treballador i vincular-lo a un usuari si s'especifica.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id'             => 'nullable|exists:users,id',
            'name'                => 'required|string|max:255',
            'role'                => 'required|string|max:255',
            'cost_per_hour'       => 'required|numeric|min:0',
            'availability'        => 'boolean',
            'location'            => 'nullable|string|max:255',
            'specialization_tags' => 'nullable|array',
            'rating'              => 'nullable|numeric|min:1|max:5',
        ]);

        $worker = Worker::create($validated);

        // Si hem triat un usuari existent, el promovem a rol 'worker'
        if ($worker->user_id) {
            $user = \App\Models\User::find($worker->user_id);
            $user->update(['role' => 'worker']);
        }

        return response()->json([
            'message' => 'Treballador creat correctament',
            'data' => $worker
        ], 201);
    }

    /**
     * Mostrar un treballador específic amb el seu usuari.
     */
    public function show(Worker $worker): JsonResponse
    {
        return response()->json($worker->load('user'));
    }

    /**
     * Actualitzar dades d'un treballador i gestionar el vincle amb l'usuari.
     */
    public function update(Request $request, Worker $worker): JsonResponse
    {
        $validated = $request->validate([
            'user_id'             => 'nullable|exists:users,id',
            'name'                => 'sometimes|required|string|max:255',
            'role'                => 'sometimes|required|string|max:255',
            'cost_per_hour'       => 'sometimes|required|numeric|min:0',
            'availability'        => 'boolean',
            'location'            => 'nullable|string|max:255',
            'specialization_tags' => 'nullable|array',
            'rating'              => 'nullable|numeric|min:1|max:5',
        ]);

        $oldUserId = $worker->user_id;
        $worker->update($validated);

        // Si el vincle ha canviat o és nou
        if ($worker->user_id && $worker->user_id != $oldUserId) {
            $user = \App\Models\User::find($worker->user_id);
            $user->update(['role' => 'worker']);
        }

        return response()->json([
            'message' => 'Treballador actualitzat correctament',
            'data' => $worker
        ]);
    }

    /**
     * Llistar usuaris que encara no són treballadors (per poder-los assignar).
     */
    public function getAvailableUsers(): JsonResponse
    {
        $users = \App\Models\User::whereDoesntHave('worker')
            ->where('role', '!=', 'admin')
            ->select('id', 'name', 'email')
            ->get();
            
        return response()->json($users);
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
