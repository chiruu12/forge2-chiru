<?php

use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\SetTenantContext;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'pulsedesk',
    ]);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);

Route::middleware(['auth:sanctum', SetTenantContext::class])
    ->prefix('tickets')
    ->group(function () {
        Route::get('/{ticket}', [TicketController::class, 'show']);
    });
