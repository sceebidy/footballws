<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FootballController;

// Route utama
Route::get('/', fn() => redirect('/football'));

// Route pencarian dengan kriteria
Route::get('/football', [FootballController::class, 'index']);

// Route untuk autosuggest dan real-time search
Route::get('/football/autosuggest', [FootballController::class, 'autosuggest']);
Route::get('/football/realtime-search', [FootballController::class, 'realtimeSearch']);

// Route pencarian spesifik berdasarkan field
Route::get('/football/country/{country}', [FootballController::class, 'byCountry']);
Route::get('/football/coach/{coach}', [FootballController::class, 'byCoach']);
Route::get('/football/stadium/{stadium}', [FootballController::class, 'byStadium']);
Route::get('/football/location/{location}', [FootballController::class, 'byLocation']);
Route::get('/football/owner/{owner}', [FootballController::class, 'byOwner']);

// Route detail klub
Route::get('/football/{id}', [FootballController::class, 'show'])->name('football.show');