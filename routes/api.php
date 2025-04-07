<?php


use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    
    Route::post('/users', [UserController::class, 'store']);
});
