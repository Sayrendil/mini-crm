<?php

use App\Http\Controllers\Api\TicketController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;


Route::post('/tickets/store', [TicketController::class, 'store']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tickets/statistics', [AdminTicketController::class, 'statistics']); // статистика
});
