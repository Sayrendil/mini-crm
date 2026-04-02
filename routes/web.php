<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('widget.index');
});

Route::get('/widget', function () {
    return view('widget.index');
})->name('widget');
