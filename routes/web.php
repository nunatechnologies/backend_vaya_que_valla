<?php

use App\Models\User;
use App\Notifications\VerifyApiEmail;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-verification-email', function () {
    $user = User::first();
    $notification = new VerifyApiEmail;

    return $notification->toMail($user)->render();
});
