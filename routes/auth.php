<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticateUserController;

Route::post('/login', [AuthenticateUserController::class, "login"]);
Route::post('/refresh', [AuthenticateUserController::class, "refresh"]);
Route::post('/logout', [AuthenticateUserController::class, "logout"]);
Route::get('/me', [AuthenticateUserController::class, 'me']);
