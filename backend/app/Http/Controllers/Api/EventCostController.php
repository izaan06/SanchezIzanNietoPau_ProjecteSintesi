<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Exception;

class EventCostController extends Controller
{
    /**
     * Predir el cost d'un esdeveniment connectant-se al microservei de Flask.
     */
    public function predict(Request $request)
    {
        // 1. Validar dades de la petició
        $validated = $request->validate([
            'type' => 'required|string',
            'assistants' => 'required|integer|min:0',
            'workers' => 'required|integer|min:0',
            'hours' => 'required|integer|min:0',
            'cost_per_hour' => 'required|numeric|min:0',
        ]);

        try {
            // URL del microservei Flask 
            // En un entorn de producció, hauries de posar això al fitxer .env com a env('FLASK_API_URL')
            $flaskUrl = env('AI_SERVICE_URL', 'http://127.0.0.1:5000') . '/predict-cost';

            // 2. Fer petició HTTP POST cap a la IA amb les dades
            // Utilitzem el Facade "Http" de Laravel
            $response = Http::timeout(10)->post($flaskUrl, $validated);

            // 3. Comprovar si la resposta és correcta
            if ($response->successful()) {
                // Retornar directament el JSON de la IA cap al Frontend
                return response()->json($response->json(), 200);
            }

            // Si hi ha error per part del microservei (ex: validacions internes del model)
            return response()->json([
                'success' => false,
                'message' => 'Error al connectar amb el microservei d\'IA.',
                'details' => $response->json()
            ], $response->status());

        } catch (Exception $e) {
            // Si el microservei està apagat o no respon (Timeout/Connection Refused)
            return response()->json([
                'success' => false,
                'message' => 'El microservei d\'IA no està disponible o està apagat.',
                'error' => $e->getMessage()
            ], 503);
        }
    }
}
