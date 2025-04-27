<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExerciseTableController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

Route::prefix('api')->middleware('jwt.auth')->group(function () {

    Route::get('/exercises', [ExerciseTableController::class, 'index']);

    Route::post('/exercises', [ExerciseTableController::class, 'store']);


    Route::delete('/exercises/{id}', [ExerciseTableController::class, 'destroy']);


    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/login', [AuthController::class, 'login']);
    
    Route::middleware('jwt.auth')->post('/logout', [
        AuthController::class, 'logout']);
});

