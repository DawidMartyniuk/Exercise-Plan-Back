<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ResetPasswordController;


Route::get('/', function () {
    return view('welcome');
});

// Klasyczny formularz  resetu (dla przeglądarki)
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset.form');

Route::get('/open-reset/{token}', function (string $token) {
    $email = request('email');

    // deep link do apki
    $deepLink = "myapp://open-reset/{$token}?email=" . urlencode($email ?? '');

    // jeśli na telefonie z apka – otworzy ją
    // jeśli na desktopie – pokaże np. komunikat
    return redirect()->away($deepLink);
})->name('password.open');