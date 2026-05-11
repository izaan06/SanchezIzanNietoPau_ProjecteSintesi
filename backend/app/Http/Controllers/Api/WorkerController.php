<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WorkerController extends Controller
{
    /**
     * Llistar tots els treballadors del sistema.
     * Carrega també els esdeveniments on participen.
     */
    public function index(): JsonResponse
    {
        $workers = Worker::with('events')->get();
        return response()->json($workers);
    }

    /**
     * Crear un nou perfil de treballador.
     * Si s'especifica un user_id, es vincula automàticament i se li assigna el rol 'worker'.
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

        // Si hem triat un usuari existent, el promovem a rol 'worker' per assegurar els permisos
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
     * Mostrar els detalls d'un treballador específic, incloent-hi les dades de l'usuari.
     */
    public function show(Worker $worker): JsonResponse
    {
        return response()->json($worker->load('user'));
    }

    /**
     * Actualitzar dades del perfil d'un treballador.
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

        // Si s'assigna a un usuari nou, actualitzem el rol de l'usuari a 'worker'
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
     * Llistar usuaris que encara no tenen un perfil de treballador assignat.
     * Útil per al formulari de creació de nous treballadors.
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
     * Eliminar el perfil d'un treballador del sistema.
     * Nota: L'usuari vinculat NO s'elimina, només el seu perfil professional.
     */
    public function destroy(Worker $worker): JsonResponse
    {
        $worker->delete();

        return response()->json([
            'message' => 'Treballador eliminat correctament'
        ]);
    }
}
