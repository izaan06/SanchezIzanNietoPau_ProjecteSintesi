<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Llistar tots els usuaris (només admin).
     */
    public function index(): JsonResponse
    {
        $users = User::with('worker')->get();
        return response()->json($users);
    }

    /**
     * Actualitzar el rol d'un usuari.
     */
    public function updateRole(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,worker,client',
        ]);

        $user->update(['role' => $validated['role']]);

        // Si el canviem a worker i no té registre de worker, el creem amb dades buides
        if ($user->role === 'worker' && !$user->worker) {
            $user->worker()->create([
                'name' => $user->name,
                'role' => 'Per definir',
                'cost_per_hour' => 0,
                'availability' => true
            ]);
        } 
        // Si el canviem a un altre rol que no és worker, eliminem el registre de worker
        elseif ($user->role !== 'worker' && $user->worker) {
            $user->worker->delete();
        }

        return response()->json([
            'message' => 'Rol actualitzat correctament',
            'user' => $user->load('worker')
        ]);
    }

    /**
     * Eliminar un usuari.
     */
    public function destroy(User $user): JsonResponse
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'No et pots eliminar a tu mateix'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'Usuari eliminat correctament']);
    }
}
