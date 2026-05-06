<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppointmentRequest;
use Illuminate\Http\Request;
use App\Mail\AppointmentApprovedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class AppointmentController extends Controller
{
    /**
     * Guardar una nova sol·licitud i demanar pressupost a la IA.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                 => 'required|string|max:255',
            'email'                => 'required|email|max:255',
            'phone'                => 'nullable|string|max:20',
            'event_type'           => 'required|string|max:100',
            'attendees'            => 'nullable|integer|min:1',
            'desired_date'         => 'required|date|after_or_equal:' . now()->addMonth()->toDateString(),
            'start_time'           => 'nullable|string',
            'end_time'             => 'nullable|string',
            'location_name'        => 'nullable|string|max:255',
            'wants_music'          => 'nullable|boolean',
            'music_type'           => 'nullable|string|max:255',
            'menu_type'            => 'nullable|string|max:255',
            'dietary_requirements' => 'nullable|string|max:2000',
            'message'              => 'nullable|string|max:2000',
            'client_budget'        => 'nullable|numeric|min:0',
        ], [
            'desired_date.after_or_equal' => 'Has de sol·licitar l\'esdeveniment amb almenys 1 mes d\'antelació.',
        ]);

        if ($request->user()) {
            $data['user_id'] = $request->user()->id;
        }

        // Crida a la IA per obtenir el pressupost estimat i l'adreça
        try {
            $aiResponse = \Illuminate\Support\Facades\Http::post('http://localhost:5000/predict-budget', [
                'event_type'    => $data['event_type'],
                'attendees'     => $data['attendees'] ?? 50,
                'location_name' => $data['location_name'] ?? '',
                'client_budget' => $data['client_budget'] ?? 0,
            ]);

            if ($aiResponse->successful()) {
                $data['ai_estimated_budget'] = $aiResponse->json('recommended_price');
                $data['ai_operational_cost'] = $aiResponse->json('estimated_cost');
                $data['ai_estimated_profit'] = $aiResponse->json('estimated_profit');
                $data['ai_recommendations']  = $aiResponse->json('recommendations');
                $data['ai_address']          = $aiResponse->json('predicted_address');
            }
        } catch (\Exception $e) {
            \Log::error("Error connectant amb el servei d'IA: " . $e->getMessage());
        }

        $appointment = AppointmentRequest::create($data);

        return response()->json([
            'message' => 'Sol·licitud enviada correctament! La nostra IA ha estimat un pressupost.',
            'data'    => $appointment,
        ], 201);
    }

    /**
     * Llistar totes les sol·licituds (només admin).
     */
    public function index()
    {
        $appointments = AppointmentRequest::latest()->get();
        return response()->json(['data' => $appointments]);
    }

    /**
     * Llistar sol·licituds de l'usuari loguejat (client).
     */
    public function myRequests(Request $request)
    {
        $appointments = AppointmentRequest::where('user_id', $request->user()->id)
            ->latest()
            ->get();
        return response()->json(['data' => $appointments]);
    }

    /**
     * Convertir una sol·licitud aprovada en un esdeveniment real amb assignació d'IA.
     */
    public function approve(Request $request, AppointmentRequest $appointment)
    {
        // 1. Generar el PDF del pressupost
        $pdf = Pdf::loadView('pdf.budget', ['appointment' => $appointment]);
        $pdfPath = 'budgets/budget_' . $appointment->id . '.pdf';
        Storage::disk('public')->put($pdfPath, $pdf->output());
        $fullPath = storage_path('app/public/' . $pdfPath);

        // 2. Enviar l'email amb el PDF adjunt
        try {
            Mail::to($appointment->email)->send(new AppointmentApprovedMail($appointment, $fullPath));
        } catch (\Exception $e) {
            \Log::error('Error enviant email: ' . $e->getMessage());
        }

        // 3. Cercar o crear el client basat en l'email de la sol·licitud
        $client = \App\Models\Client::firstOrCreate(
            ['email' => $appointment->email],
            ['name' => $appointment->name, 'phone' => $appointment->phone]
        );

        // 4. Crear l'Esdeveniment real amb les dades detallades
        $event = \App\Models\Event::create([
            'name'                   => "Esdeveniment: " . ($appointment->event_type ?? 'Nova Cita'),
            'client_id'              => $client->id,
            'appointment_request_id' => $appointment->id,
            'date'                   => $appointment->desired_date ?? now()->addMonth(),
            'location'               => $appointment->location_name ?? 'Per determinar',
            'type'                   => $appointment->event_type,
            'assistants'             => $appointment->attendees ?? 0,
            'status'                 => 'confirmat',
            'estimated_cost'         => $appointment->ai_estimated_budget ?? 0,
        ]);

        // 5. Demanar a la IA recomanacions de treballadors
        $workers = \App\Models\Worker::all();
        try {
            $aiResponse = \Illuminate\Support\Facades\Http::post('http://localhost:5000/recommend-workers', [
                'event_type' => $appointment->event_type,
                'workers'    => $workers->toArray(),
            ]);

            if ($aiResponse->successful()) {
                $recommendations = $aiResponse->json('recommendations');
                // Assignar els 3 millors automàticament
                foreach (array_slice($recommendations, 0, 3) as $rec) {
                    // Evitar duplicats si ja estiguessin assignats
                    $event->workers()->syncWithoutDetaching([$rec['id'] => ['hours' => 8]]);
                }
                $appointment->update(['ai_recommendations' => $recommendations]);
            }
        } catch (\Exception $e) {
            \Log::error("Error IA en assignació: " . $e->getMessage());
        }

        $appointment->update(['status' => 'accepted']);

        return response()->json([
            'success' => true,
            'message' => 'Sol·licitud aprovada, esdeveniment creat i treballadors assignats per IA!',
            'event'   => $event->load('workers'),
        ]);
    }

    public function update(Request $request, AppointmentRequest $appointment)
    {
        $request->validate(['status' => 'required|in:pending,reviewed,accepted,rejected']);
        $appointment->update(['status' => $request->status]);
        return response()->json(['message' => 'Estat actualitzat', 'data' => $appointment]);
    }
}
