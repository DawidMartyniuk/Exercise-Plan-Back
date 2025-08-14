<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ResetPasswordController;


Route::get('/', function () {
    return view('welcome');
});

// Klasyczny formularz resetu (dla przeglądarki)
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset.form');

// Deep link otwierający aplikację mobilną
Route::get('/open-reset/{token}', function (string $token) {
    $email = request('email'); // pobiera ?email=...
    $deepLink = "myapp://reset-password?token=" . urlencode($token) . "&email=" . urlencode($email ?? '');

    // Przekierowanie – jeśli appka jest zainstalowana, system zaproponuje jej otwarcie
    return redirect()->away($deepLink);
})->name('password.open');
