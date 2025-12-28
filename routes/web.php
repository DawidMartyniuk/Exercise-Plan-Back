<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use L5Swagger\Http\Controllers\SwaggerController;

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

    return redirect()->away($deepLink);
})->name('password.open');


Route::get('/storage/gifs/{filename}', function ($filename) {
    $path = storage_path('app/public/gifs/' . $filename);
    if (!file_exists($path)) abort(404);

    $mimeType = mime_content_type($path);
    return response()->file($path, [
        'Content-Type' => $mimeType,
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS, DELETE, PUT',
        'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
    ]);
})->where('filename', '.*\.(gif|jpg|jpeg|png|webp|svg)$');
Route::get('/test-image', function () {
    return response()->json([
        'ok' => true,
        'time' => now(),
        'ip' => request()->ip(),
    ])->header('Access-Control-Allow-Origin', '*');
});


Route::get('/api/documentation', [SwaggerController::class, 'api'])->name('l5swagger.default.api');


Route::get('/docs', [SwaggerController::class, 'api']); // otwórz /docs
// ...existing code...