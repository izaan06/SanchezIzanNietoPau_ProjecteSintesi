<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\WorkerController;
use App\Http\Controllers\Api\EventWorkerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EventCostController;
use App\Http\Controllers\Api\WorkerDashboardController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TimeOffController;
use Illuminate\Support\Facades\Route;

// ==========================================
// RUTES PÚBLIQUES (Sense autenticació)
// ==========================================
// Aquestes rutes són accessibles per a tothom, ja que serveixen justament per autenticar-se.
Route::post('/register', [AuthController::class, 'register']); // Crea un nou usuari
Route::post('/login', [AuthController::class, 'login']);    // Inicia sessió i retorna el Token JWT/Sanctum

// ==========================================
// RUTES PROTEGIDES (Requereixen Token)
// ==========================================
// Només els usuaris que enviïn un Token vàlid a la capçalera (Bearer Token) poden accedir aquí.
Route::middleware('auth:sanctum')->group(function () {

    // -- Perfil i Sessió --
    Route::get('/me', [AuthController::class, 'me']);       // Retorna les dades de l'usuari actual
    Route::post('/logout', [AuthController::class, 'logout']);   // Destrueix el token i tanca la sessió

    // -- Sol·licituds (Appointments) globals --
    Route::get('/my-appointments', [AppointmentController::class, 'myRequests']); // Veure les meves sol·licituds
    Route::post('/appointment-request', [AppointmentController::class, 'store']); // Crear una nova sol·licitud

    // ==========================================
    // RUTES DE TREBALLADOR (I ADMIN)
    // ==========================================
    // Middleware específic: Només els usuaris amb rol 'worker' o 'admin' poden entrar.
    Route::middleware('role:worker,admin')->group(function () {
        Route::get('/worker/my-events', [WorkerDashboardController::class, 'myEvents']);    // Esdeveniments assignats
        Route::get('/worker/my-colleagues', [WorkerDashboardController::class, 'myColleagues']);// Companys de feina

        // -- Gestió de Vacances / Festes (Part del treballador) --
        Route::get('/worker/time-off', [TimeOffController::class, 'index']); // Llistar les meves vacances
        Route::post('/worker/time-off', [TimeOffController::class, 'store']); // Sol·licitar noves vacances
    });

    // ==========================================
    // RUTES D'ADMINISTRADOR
    // ==========================================
    // Middleware estricte: Només els administradors tenen accés a aquestes rutes.
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']); // Dades globals per a les gràfiques

        // -- Recursos complets (CRUD automàtic de Laravel: index, store, show, update, destroy) --
        Route::apiResource('clients', ClientController::class);
        Route::apiResource('events', EventController::class);

        // -- Treballadors --
        Route::get('/workers/available-users', [WorkerController::class, 'getAvailableUsers']); // Llistar usuaris lliures
        Route::apiResource('workers', WorkerController::class); // CRUD de treballadors

        // -- Assignació de Treballadors a Esdeveniments --
        Route::get('/events/{event}/workers', [EventWorkerController::class, 'getWorkers']);   // Veure treballadors d'un esdeveniment
        Route::post('/events/{event}/workers', [EventWorkerController::class, 'assignWorker']); // Assignar-ne un de nou
        Route::delete('/events/{event}/workers/{worker}', [EventWorkerController::class, 'removeWorker']);// Desassignar-lo

        // -- Connexió amb el Microservei d'IA --
        Route::post('/events/predict-cost', [EventCostController::class, 'predict']);

        // -- Gestió Sol·licituds de Cita (Appointments) --
        Route::get('/appointments', [AppointmentController::class, 'index']);   // Llistar totes les sol·licituds
        Route::patch('/appointments/{appointment}', [AppointmentController::class, 'update']); // Actualitzar-ne una
        Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy']);// Esborrar-la
        Route::post('/appointments/{appointment}/approve', [AppointmentController::class, 'approve']); // Aprovar-la i convertir-la en Event

        // -- Gestió d'Usuaris Globals (Pau i Izan) --
        Route::get('/users', [UserController::class, 'index']); // Llistar tothom
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole']); // Canviar rol (ex: de user a admin)
        Route::delete('/users/{user}', [UserController::class, 'destroy']); // Eliminar usuari del sistema

        // -- Gestió de Vacances (Part de l'Admin) --
        Route::get('/admin/time-off', [TimeOffController::class, 'index']); // Llistar vacances de tots els treballadors
        Route::patch('/admin/time-off/{timeOff}', [TimeOffController::class, 'updateStatus']); // Aprovar o denegar vacances

    });
});
