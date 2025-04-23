<?php

use App\Models\User;
use App\Notifications\ResetPasswordCustom;
use App\Notifications\VerifyApiEmail;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/test-verification-email', function () {
//     $user = User::first();
//     $notification = new VerifyApiEmail;

//     return $notification->toMail($user)->render();
// });

Route::get('/test-reset-password-email', function () {
    $user = User::first();

    // Crear el token como lo hace Laravel internamente
    $token = Password::broker()->createToken($user);

    // Crear la notificación personalizada con el token
    $notification = new ResetPasswordCustom($token);

    // Renderizar el correo sin enviarlo
    return $notification->toMail($user)->render();
});
