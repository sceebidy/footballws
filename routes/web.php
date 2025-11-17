<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FootballController;


Route::get('/', [FootballController::class, 'index'])->name('football.index');

Route::get('/club/{id}', [FootballController::class, 'show'])->name('club.show');

// Route utama
Route::get('/', fn() => redirect('/football'));

// Route pencarian dasar
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
Route::get('/football/player/{player}', [FootballController::class, 'byPlayer']);

// Route untuk data pemain
Route::get('/football/players', [FootballController::class, 'getAllPlayers']);

// Route detail klub
Route::get('/football/{id}', [FootballController::class, 'show'])->name('football.show');