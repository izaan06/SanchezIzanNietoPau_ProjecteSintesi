<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    /**
     * Llistar tots els clients.
     */
    public function index(): JsonResponse
    {
        $clients = Client::all();
        return response()->json($clients);
    }

    /**
     * Crear un nou client.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $client = Client::create($validated);

        return response()->json([
            'message' => 'Client creat correctament',
            'data' => $client
        ], 201);
    }

    /**
     * Mostrar un client específic.
     */
    public function show(Client $client): JsonResponse
    {
        return response()->json($client);
    }

    /**
     * Actualitzar dades d'un client.
     */
    public function update(Request $request, Client $client): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:clients,email,' . $client->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $client->update($validated);

        return response()->json([
            'message' => 'Client actualitzat correctament',
            'data' => $client
        ]);
    }

    /**
     * Eliminar un client.
     */
    public function destroy(Client $client): JsonResponse
    {
        $client->delete();

        return response()->json([
            'message' => 'Client eliminat correctament'
        ]);
    }
}
