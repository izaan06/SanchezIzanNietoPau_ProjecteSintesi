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

// --- Rutes públiques ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// --- Rutes protegides (qualsevol usuari autenticat) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me',     [AuthController::class, 'me']);
    Route::post('/logout',[AuthController::class, 'logout']);
    Route::get('/my-appointments', [AppointmentController::class, 'myRequests']);
    Route::post('/appointment-request', [AppointmentController::class, 'store']);

    // --- Rutes de TREBALLADOR ---
    Route::middleware('role:worker,admin')->group(function () {
        Route::get('/worker/my-events',    [WorkerDashboardController::class, 'myEvents']);
        Route::get('/worker/my-colleagues',[WorkerDashboardController::class, 'myColleagues']);
        
        // Vacances / Festes
        Route::get('/worker/time-off',    [TimeOffController::class, 'index']);
        Route::post('/worker/time-off',   [TimeOffController::class, 'store']);
    });

    // --- Rutes d'ADMIN ---
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);

        Route::apiResource('clients', ClientController::class);
        Route::apiResource('events',  EventController::class);
        Route::get('/workers/available-users', [WorkerController::class, 'getAvailableUsers']);
        Route::apiResource('workers', WorkerController::class);

        Route::get('/events/{event}/workers',           [EventWorkerController::class, 'getWorkers']);
        Route::post('/events/{event}/workers',          [EventWorkerController::class, 'assignWorker']);
        Route::delete('/events/{event}/workers/{worker}',[EventWorkerController::class, 'removeWorker']);

        Route::post('/events/predict-cost', [EventCostController::class, 'predict']);

        // Gestió sol·licituds de cita
        Route::get('/appointments',          [AppointmentController::class, 'index']);
        Route::patch('/appointments/{appointment}', [AppointmentController::class, 'update']);
        Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy']);
        Route::post('/appointments/{appointment}/approve', [AppointmentController::class, 'approve']);

        // Gestió d'Usuaris (Pau i Izan)
        Route::get('/users', [UserController::class, 'index']);
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);

        // Gestió de Vacances (Admin)
        Route::get('/admin/time-off',     [TimeOffController::class, 'index']);
        Route::patch('/admin/time-off/{timeOff}', [TimeOffController::class, 'updateStatus']);

    });
});
