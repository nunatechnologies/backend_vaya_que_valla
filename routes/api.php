<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BillboardController;
use App\Http\Controllers\BillboardFaceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'jwt.auth'])->get('/me', function (Request $request) {
    return response()->json(auth()->user());
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'authen'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    // Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    // Route::put('reset-password', [ForgotPasswordController::class, 'resetPassword']);
    // Route::get('verify-token/{token}/{email}', [ForgotPasswordController::class, 'verifyTokenResetPassword']);
});

Route::group(['middleware' => ['api', 'jwt.auth']], function () {
    Route::get('/billboards', [BillboardController::class, 'list_billboard_pagination']);
    Route::get('/billboard-faces', [BillboardFaceController::class, 'list_billboard_face_pagination']);
});