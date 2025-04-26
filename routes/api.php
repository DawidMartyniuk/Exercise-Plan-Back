<?php


use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/login', [AuthController::class, 'login']);
    
    Route::middleware('jwt.auth')->post('/logout', [
        AuthController::class, 'logout']);
});
