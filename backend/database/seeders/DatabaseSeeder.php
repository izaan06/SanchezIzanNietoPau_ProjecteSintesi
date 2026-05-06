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
            'name'     => 'Admin EventAI',
            'email'    => 'admin@eventai.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // --- TREBALLADORS (User + Worker vinculats) ---
        $workerUser1 = User::create([
            'name'     => 'Maria García',
            'email'    => 'maria@eventai.com',
            'password' => Hash::make('password'),
            'role'     => 'worker',
        ]);
        Worker::create([
            'user_id'      => $workerUser1->id,
            'name'         => 'Maria García',
            'role'         => 'Coordinadora',
            'cost_per_hour'=> 25.00,
            'availability' => true,
        ]);

        $workerUser2 = User::create([
            'name'     => 'Joan Martínez',
            'email'    => 'joan@eventai.com',
            'password' => Hash::make('password'),
            'role'     => 'worker',
        ]);
        Worker::create([
            'user_id'      => $workerUser2->id,
            'name'         => 'Joan Martínez',
            'role'         => 'Tècnic de So',
            'cost_per_hour'=> 20.00,
            'availability' => true,
        ]);

        // --- CLIENT ---
        User::create([
            'name'     => 'Client Demo',
            'email'    => 'client@demo.com',
            'password' => Hash::make('password'),
            'role'     => 'client',
        ]);
    }
}
