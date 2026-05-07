<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Worker;
use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- ADMIN ---
        User::create([
            'name' => 'Admin EventAI',
            'email' => 'admin@eventai.com',
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
        Worker::create([
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
        Worker::create([
            'user_id' => $workerUser2->id,
            'name' => 'Joan Martínez',
            'role' => 'Tècnic de So',
            'cost_per_hour' => 20.00,
            'availability' => true,
        ]);

        // 3. Izan (Personalitzat per al projecte)
        $workerUser3 = User::create([
            'name' => 'Izan',
            'email' => 'izansaanchez06@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'worker',
        ]);
        Worker::create([
            'user_id' => $workerUser3->id,
            'name' => 'Izan',
            'role' => 'Especialista IA',
            'cost_per_hour' => 35.00,
            'availability' => true,
        ]);

        // --- CLIENTS ---
        User::create([
            'name' => 'Client Demo',
            'email' => 'client@demo.com',
            'password' => Hash::make('password'),
            'role' => 'client',
        ]);

        Client::create([
            'name' => 'Client Demo',
            'email' => 'client@demo.com',
            'phone' => '600000000',
            'address' => 'Empresa de Prova S.L.'
        ]);
    }
}
