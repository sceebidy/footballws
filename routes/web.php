<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FootballController;
use App\Http\Controllers\SoccerController;

// route utama
Route::get('/soccer', [SoccerController::class, 'index'])->name('soccer.index');

// route opsional agar /soccer/index juga bisa diakses
Route::get('/soccer/index', [SoccerController::class, 'index']);

Route::get('/football', [FootballController::class, 'index']);
Route::get('/football/{id}', [FootballController::class, 'show'])->name('football.show');
Route::get('/', fn() => redirect('/football'));

