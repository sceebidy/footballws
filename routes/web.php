<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FootballController;

Route::get('/football', [FootballController::class, 'index']);
Route::get('/football/{id}', [FootballController::class, 'show'])->name('football.show');
Route::get('/', fn() => redirect('/football'));
Route::get('/league/{id}', [App\Http\Controllers\FootballController::class, 'league'])
    ->name('league.show');

