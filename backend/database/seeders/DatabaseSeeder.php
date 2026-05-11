<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Worker;
use App\Models\Client;
use App\Models\Event;
use App\Models\AppointmentRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Punt d'entrada del Seeder. Defineix les dades inicials de la base de dades.
     */
    public function run(): void
    {
        // --- ADMINS (Gestors del sistema) ---
        User::create([
            'name' => 'Admin EventAI',
            'email' => 'admin@eventai.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Pau Nieto',
            'email' => 'pau@eventai.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // --- TREBALLADORS (User + Worker vinculats) ---

        // 1. Maria
        $workerUser1 = User::create([
            'name' => 'Maria García',
            'email' => 'maria@eventai.com',
            'password' => Hash::make('password'),
            'role' => 'worker',
        ]);
        $worker1 = Worker::create([
            'user_id' => $workerUser1->id,
            'name' => 'Maria García',
            'role' => 'Coordinadora',
            'cost_per_hour' => 25.00,
            'availability' => true,
        ]);

        // 2. Joan
        $workerUser2 = User::create([
            'name' => 'Joan Martínez',
            'email' => 'joan@eventai.com',
            'password' => Hash::make('password'),
            'role' => 'worker',
        ]);
        $worker2 = Worker::create([
            'user_id' => $workerUser2->id,
            'name' => 'Joan Martínez',
            'role' => 'Tècnic de So',
            'cost_per_hour' => 20.00,
            'availability' => true,
        ]);

        // 3. Izan
        $workerUser3 = User::create([
            'name' => 'Izan',
            'email' => 'izansaanchez06@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'worker',
        ]);
        $worker3 = Worker::create([
            'user_id' => $workerUser3->id,
            'name' => 'Izan',
            'role' => 'Especialista IA',
            'cost_per_hour' => 35.00,
            'availability' => true,
        ]);

        // 4. Laura
        $workerUser4 = User::create([
            'name' => 'Laura Soler',
            'email' => 'laura@eventai.com',
            'password' => Hash::make('password'),
            'role' => 'worker',
        ]);
        $worker4 = Worker::create([
            'user_id' => $workerUser4->id,
            'name' => 'Laura Soler',
            'role' => 'Fotògrafa',
            'cost_per_hour' => 30.00,
            'availability' => true,
        ]);

        // 5. Marc
        $workerUser5 = User::create([
            'name' => 'Marc Vila',
            'email' => 'marc@eventai.com',
            'password' => Hash::make('password'),
            'role' => 'worker',
        ]);
        $worker5 = Worker::create([
            'user_id' => $workerUser5->id,
            'name' => 'Marc Vila',
            'role' => 'DJ & Il·luminació',
            'cost_per_hour' => 40.00,
            'availability' => true,
        ]);

        // --- CLIENTS ---
        
        // Client 1
        $client1User = User::create([
            'name' => 'Client Demo',
            'email' => 'client@demo.com',
            'password' => Hash::make('password'),
            'role' => 'client',
        ]);
        $client1 = Client::create([
            'name' => 'Client Demo',
            'email' => 'client@demo.com',
            'phone' => '600000000',
            'address' => 'Empresa de Prova S.L.'
        ]);

        // Client 2
        $client2User = User::create([
            'name' => 'Hotel Arts',
            'email' => 'events@hotelarts.com',
            'password' => Hash::make('password'),
            'role' => 'client',
        ]);
        $client2 = Client::create([
            'name' => 'Hotel Arts Barcelona',
            'email' => 'events@hotelarts.com',
            'phone' => '932211000',
            'address' => 'Carrer de la Marina, 19, Barcelona'
        ]);

        // --- ESDEVENIMENTS PER DEFECTE ---

        // 1. Esdeveniment: Boda
        $req1 = AppointmentRequest::create([
            'name' => 'Maria i Joan',
            'email' => 'client@demo.com',
            'event_type' => 'Boda',
            'attendees' => 150,
            'desired_date' => now()->addDays(30),
            'status' => 'accepted',
            'ai_estimated_budget' => 12000.00,
            'ai_operational_cost' => 4500.00,
            'ai_estimated_profit' => 7500.00,
            'ai_recommendations' => ['Optimitzar càtering', 'Afegir il·luminació LED'],
            'wants_music' => true,
            'menu_type' => 'Premium',
            'start_time' => '18:00',
            'end_time' => '23:30'
        ]);

        $event1 = Event::create([
            'name' => 'Boda de Maria i Joan',
            'date' => now()->addDays(30),
            'location' => 'Manresa, Masia Can Font',
            'type' => 'Boda',
            'assistants' => 150,
            'status' => 'confirmed',
            'client_id' => $client1->id,
            'appointment_request_id' => $req1->id,
            'tasks' => [
                ['text' => 'Confirmar càtering', 'completed' => true],
                ['text' => 'Proves de so', 'completed' => false],
                ['text' => 'Decoració floral', 'completed' => false]
            ]
        ]);

        // Assignar treballadors a la boda
        $event1->workers()->attach($worker1->id, ['hours' => 10]);
        $event1->workers()->attach($worker4->id, ['hours' => 8]);

        // 2. Esdeveniment: Corporatiu
        $req2 = AppointmentRequest::create([
            'name' => 'Conferència Tech',
            'email' => 'events@hotelarts.com',
            'event_type' => 'Corporatiu',
            'attendees' => 500,
            'desired_date' => now()->addDays(60),
            'status' => 'accepted',
            'ai_estimated_budget' => 25000.00,
            'ai_operational_cost' => 12000.00,
            'ai_estimated_profit' => 13000.00,
            'ai_recommendations' => ['Zona de networking ampliada'],
            'wants_music' => false,
            'menu_type' => 'Buffet',
            'start_time' => '09:00',
            'end_time' => '17:00'
        ]);

        $event2 = Event::create([
            'name' => 'Conferència Anual Tech',
            'date' => now()->addDays(60),
            'location' => 'Hotel Arts Barcelona',
            'type' => 'Corporatiu',
            'assistants' => 500,
            'status' => 'planning',
            'client_id' => $client2->id,
            'appointment_request_id' => $req2->id,
            'tasks' => [
                ['text' => 'Reserva de sales', 'completed' => true],
                ['text' => 'Llistat de ponents', 'completed' => true]
            ]
        ]);

        // Assignar treballadors al corporatiu
        $event2->workers()->attach($worker2->id, ['hours' => 12]);
        $event2->workers()->attach($worker3->id, ['hours' => 15]);
    }
}
