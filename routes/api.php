<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExerciseTableController;
use App\Http\Controllers\TrainingSesionsController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

// Ścieżki, które nie wymagają jwt.auth
Route::prefix('api')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/login', [AuthController::class, 'login']);
    
});

// Ścieżki, które wymagają jwt.auth
Route::prefix('api')->middleware('jwt.auth')->group(function () {

    Route::get('/profile', [UsersController::class, 'getProfile']);

    Route::put('/profile', [UsersController::class, 'updateProfile']);

    Route::post('/profile/avatar', [UsersController::class, 'updateAvatar']);

    Route::delete('/profile/avatar', [UsersController::class, 'deleteAvatar']);


    Route::get('/exercises', [ExerciseTableController::class, 'index']);

    Route::post('/exercises', [ExerciseTableController::class, 'store']);

    Route::delete('/exercises/{id}', [ExerciseTableController::class, 'destroy']);


    Route::get('/training-sessions', [TrainingSesionsController::class, 'index']);

    Route::post('/training-sessions', [TrainingSesionsController::class, 'store']);
    

     Route::post('/logout', [AuthController::class, 'logout']);
});
