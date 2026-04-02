<?php

use App\Http\Controllers\Api\TicketController;
use Illuminate\Support\Facades\Route;


Route::post('/tickets/store', [TicketController::class, 'store']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tickets/statistics', [TicketController::class, 'statistics']); // статистика
});
