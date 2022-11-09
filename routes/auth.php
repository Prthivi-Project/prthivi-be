<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticateUserController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\RegisterUserController;
use App\Http\Controllers\Auth\VerifyEmailController;

Route::post('/register', [RegisterUserController::class, "register"])->middleware('guest');
Route::post('/login', [AuthenticateUserController::class, "login"])->middleware('guest');
Route::post('/refresh', [AuthenticateUserController::class, "refresh"])->middleware('jwt.verify');
Route::post('/logout', [AuthenticateUserController::class, "logout"])->middleware('jwt.verify');
Route::get('/me', [AuthenticateUserController::class, 'me'])->middleware('jwt.verify');

Route::get('/verify-email/{id}/{token}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['jwt.verify', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['jwt.verify', 'throttle:6,1'])
    ->name('verification.send');
