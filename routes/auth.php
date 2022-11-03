<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticateUserController;
use App\Http\Controllers\Auth\RegisterUserController;

Route::post('/register', [RegisterUserController::class, "register"]);
Route::post('/login', [AuthenticateUserController::class, "login"]);
Route::post('/refresh', [AuthenticateUserController::class, "refresh"]);
Route::post('/logout', [AuthenticateUserController::class, "logout"]);
Route::get('/me', [AuthenticateUserController::class, 'me']);
