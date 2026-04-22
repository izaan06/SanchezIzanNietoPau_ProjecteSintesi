<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rutes públiques
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\WorkerController;
use App\Http\Controllers\Api\EventWorkerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EventCostController;

// Rutes protegides
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    // CRUD de Clients
    Route::apiResource('clients', ClientController::class);

    // CRUD d'Esdeveniments
    Route::apiResource('events', EventController::class);

    // CRUD de Treballadors
    Route::apiResource('workers', WorkerController::class);

    // Assignació de Treballadors a Esdeveniments
    Route::post('/events/{event}/workers', [EventWorkerController::class, 'assignWorker']);
    Route::delete('/events/{event}/workers/{worker}', [EventWorkerController::class, 'removeWorker']);

    // Predicció de costos amb Intel·ligència Artificial
    Route::post('/events/predict-cost', [EventCostController::class, 'predict']);
});
