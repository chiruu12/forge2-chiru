<?php

use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'pulsedesk',
    ]);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('tenant')
    ->prefix('tickets')
    ->group(function () {
        Route::get('/', [TicketController::class, 'index']);
        Route::post('/', [TicketController::class, 'store']);
        Route::get('/{ticket}', [TicketController::class, 'show']);
        Route::put('/{ticket}', [TicketController::class, 'update']);
        Route::delete('/{ticket}', [TicketController::class, 'destroy']);
        Route::post('/{ticket}/comments', [CommentController::class, 'store']);
    });

Route::middleware('tenant')
    ->get('/dashboard/metrics', [DashboardController::class, 'metrics']);
