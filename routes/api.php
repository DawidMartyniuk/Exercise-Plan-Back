<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExerciseTableController;
use App\Http\Controllers\TrainingSesionsController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;


// Routy bez jwt.auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/reset-request', [ResetPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->name('password.reset'); 


// Routy z jwt.auth
Route::middleware('jwt.auth')->group(function () {
    Route::get('/profile', [UsersController::class, 'getProfile']);
    Route::put('/profile', [UsersController::class, 'updateProfile']);
    Route::post('/profile/avatar', [UsersController::class, 'updateAvatar']);
    Route::delete('/profile/avatar', [UsersController::class, 'deleteAvatar']);

    Route::get('/exercises', [ExerciseTableController::class, 'index']);
    Route::post('/exercises', [ExerciseTableController::class, 'store']);
    Route::delete('/exercises/{id}', [ExerciseTableController::class, 'destroy']);

    Route::get('/training-sessions', [TrainingSesionsController::class, 'index']);
    Route::post('/training-sessions', [TrainingSesionsController::class, 'store']);
    Route::delete('/training-sessions/{id}', [TrainingSesionsController::class, 'delete']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
