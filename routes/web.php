<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FootballController;

Route::get('/football', [FootballController::class, 'index']);
Route::get('/', fn() => redirect('/football'));