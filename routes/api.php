<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExerciseTableController;
use App\Http\Controllers\TrainingSesionsController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ExerciseController;
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

    Route::get('/plan', [ExerciseTableController::class, 'index']);
    Route::post('/plan', [ExerciseTableController::class, 'store']);
    Route::delete('/plan/{id}', [ExerciseTableController::class, 'destroy']);
    Route::put('/plan/{id}', [ExerciseTableController::class, 'update']);

    Route::get('/training-sessions', [TrainingSesionsController::class, 'index']);
    Route::post('/training-sessions', [TrainingSesionsController::class, 'store']);
    Route::delete('/training-sessions/{id}', [TrainingSesionsController::class, 'delete']);
    Route::put('/training-sessions/{id}', [TrainingSesionsController::class, 'update']);

    Route::get('/exercises', [ExerciseController::class, 'index']);
    Route::post('/exercises', [ExerciseController::class, 'create']);
    //TODO: dorobić edytowanie i usuwanie ćwiczeń

    Route::post('/logout', [AuthController::class, 'logout']);
});
Route::get('/test-image', function() {
    return response()->json([
        'image_url' => asset('storage/gifs/aPFlkyJHmq5Wwso5V8XSZBHjF7pVRAMRuydPseEr.png')
    ]);
});