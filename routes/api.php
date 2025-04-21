<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\BillboardController;
use App\Http\Controllers\BillboardFaceController;
use App\Http\Controllers\BillboardTypeController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\QuoteRequestController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/email/verify/{id}/{hash}', function (Request $request) {
    // Cargar el usuario manualmente mediante el id de la URL
    $user = User::findOrFail($request->route('id'));

    // Validar que el hash concuerde con el email del usuario
    if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
        return response()->json(['message' => 'El enlace de verificación es inválido.'], 403);
    }

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'El email ya fue verificado.'], 200);
    }

    $user->markEmailAsVerified();

    return response()->json(['message' => 'Email verificado correctamente'], 200);
})->middleware(['signed'])->name('verification.verify');

Route::post('/email/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response()->json(['message' => 'Correo de verificación reenviado']);
})->middleware(['auth:api'])->name('verification.send');

Route::group(['prefix' => 'authen'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('externals/receive-request', [RequestController::class, 'receiveExternalRequest']);
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    // Route::put('reset-password', [ForgotPasswordController::class, 'resetPassword']);
    // Route::get('verify-token/{token}/{email}', [ForgotPasswordController::class, 'verifyTokenResetPassword']);
});

Route::group(['middleware' => ['api', 'jwt.auth']], function () {
    Route::put('/change_password', [ForgotPasswordController::class, 'changePassword']);
    //Users
    Route::post('/users', [UserController::class, 'register']);
    Route::post('/users/{id}/rol', [UserController::class, 'update_rol']);
    Route::put('/users/{id}', [UserController::class, 'update_user']);
    Route::get('/users/{id}', [UserController::class, 'get_user']);
    Route::get('/users', [UserController::class, 'list_user_pagination']);
    // Route::get('/roles', [UserController::class, 'all_roles']);
    
    //Cities
    Route::get('/cities', [CityController::class, 'list_city_pagination']);
    Route::post('/cities', [CityController::class, 'register']);
    Route::get('/cities/{id}', [CityController::class, 'get_city']);
    Route::put('/cities/{id}', [CityController::class, 'update_city']);

    //Quotes
    Route::get('/quotes', [QuoteController::class, 'list_quote_pagination']);
    Route::post('/quotes', [QuoteController::class, 'register']);
    Route::get('/quotes/{id}', [QuoteController::class, 'get_quote']);
    Route::put('/quotes/{id}', [QuoteController::class, 'update_quote']);

    //People
    Route::get('/people', [PersonController::class, 'list_person_pagination']);
    Route::post('/people', [PersonController::class, 'register']);
    Route::get('/people/{id}', [PersonController::class, 'get_person']);
    Route::put('/people/{id}', [PersonController::class, 'update_person']);

    //Organizations
    Route::get('/organizations', [OrganizationController::class, 'list_organization_pagination']);
    Route::post('/organizations', [OrganizationController::class, 'register']);
    Route::get('/organizations/{id}', [OrganizationController::class, 'get_organization']);
    Route::put('/organizations/{id}', [OrganizationController::class, 'update_organization']);

    //Rentals
    Route::get('/rentals', [RentalController::class, 'list_rental_pagination']);
    Route::post('/rentals', [RentalController::class, 'register']);
    Route::get('/rentals/{id}', [RentalController::class, 'get_rental']);
    Route::put('/rentals/{id}', [RentalController::class, 'update_rental']);

    //Billboard types
    Route::get('/billboard_types', [BillboardTypeController::class, 'list_billboardtype_pagination']);
    Route::post('/billboard_types', [BillboardTypeController::class, 'register']);
    Route::get('/billboard_types/{id}', [BillboardTypeController::class, 'get_billboardtype']);
    Route::put('/billboard_types/{id}', [BillboardTypeController::class, 'update_billboardtype']);

    //Billboards
    Route::get('/billboards', [BillboardController::class, 'list_billboard_pagination']);
    Route::post('/billboards', [BillboardController::class, 'register']);
    Route::get('/billboards/{id}', [BillboardController::class, 'get_billboard']);
    Route::put('/billboards/{id}', [BillboardController::class, 'update_billboard']);

    //Billboard faces
    Route::get('/billboard_faces', [BillboardFaceController::class, 'list_billboardface_pagination']);
    Route::post('/billboard_faces', [BillboardFaceController::class, 'register']);
    Route::get('/billboard_faces/{id}', [BillboardFaceController::class, 'get_billboardface']);
    Route::put('/billboard_faces/{id}', [BillboardFaceController::class, 'update_billboardface']);
    
    //Roles
    Route::get('/roles', [RoleController::class, 'list_role_pagination']);
    Route::post('/roles', [RoleController::class, 'register']);
    Route::get('/roles/{id}', [RoleController::class, 'get_role']);
    Route::put('/roles/{id}', [RoleController::class, 'update_role']);

    //Requests
    Route::get('/requests', [RequestController::class, 'list_request_pagination']);
    Route::post('/requests', [RequestController::class, 'register']);
    Route::get('/requests/{id}', [RequestController::class, 'get_request']);
    Route::put('/requests/{id}', [RequestController::class, 'update_request']);

    //Quote requests
    Route::get('/quote_request', [QuoteRequestController::class, 'list_quoterequest_pagination']);
    Route::post('/quote_request', [QuoteRequestController::class, 'register']);
    Route::get('/quote_request/{id}', [QuoteRequestController::class, 'get_quoterequest']);
    Route::put('/quote_request/{id}', [QuoteRequestController::class, 'update_quoterequest']);

    //Dashboard
    Route::get('/dashboard/general', [DashboardController::class, 'getGeneralStatistics']);
});

