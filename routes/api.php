<?php

use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\TicketController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:tickets-api', 'audit.api:q'])->group(function () {
    Route::get('/tickets/open', [TicketController::class, 'open']);
    Route::get('/tickets/closed', [TicketController::class, 'closed']);
    Route::get('/users/{user}/tickets', [TicketController::class, 'byUser']);

    Route::get('/stats', [StatsController::class, 'index']);
});
