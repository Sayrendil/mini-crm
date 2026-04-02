<?php

use App\Http\Controllers\Api\TicketController;
use Illuminate\Support\Facades\Route;


// Группа для работы с заявками
Route::post('/tickets', [TicketController::class, 'store']);
Route::get('/tickets/statistics', [TicketController::class, 'statistics']);
