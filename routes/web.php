<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ResetPasswordController;

Route::get('/', function () {
    return view('welcome');
});

// GET route dla formularza
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset.form');

// POST route dla przetwarzania (w web.php)
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->name('password.reset'); // Zachowaj tę nazwę w web.php