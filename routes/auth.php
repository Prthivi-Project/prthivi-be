<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticateUserController;
use App\Http\Controllers\Auth\RegisterUserController;

Route::post('/register', [RegisterUserController::class, "register"])->middleware('guest');
Route::post('/login', [AuthenticateUserController::class, "login"])->middleware('guest');
Route::post('/refresh', [AuthenticateUserController::class, "refresh"])->middleware('jwt.verify');
Route::post('/logout', [AuthenticateUserController::class, "logout"])->middleware('jwt.verify');
Route::get('/me', [AuthenticateUserController::class, 'me'])->middleware('jwt.verify');
