<?php

namespace App\Http\Controllers\Auth;

use App\Enums\SeveritySystemLog;
use App\Http\Controllers\Controller;
use App\Http\Messages\SuccessMessages;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Responses\ApiResponse;
use App\Services\SystemLogService;
use App\Services\User\AuthService;

class ForgotPasswordController extends Controller
{
    protected $authService;
    protected $SystemLogService;

    public function __construct(
        AuthService $authService,
        SystemLogService $SystemLogService
    ) {
        $this->authService = $authService;
        $this->SystemLogService = $SystemLogService;
    }

    /**
     * @OA\Put(
     *     path="/api/change_password",
     *     summary="Cambiar la contraseña del usuario autenticado",
     *     tags={"Authentication"},
     *   security={{ "bearerAuth": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password", "new_password", "new_password_confirmation"},
     *             @OA\Property(property="current_password", type="string", example="current_password"),
     *             @OA\Property(property="new_password", type="string", example="new_password"),
     *             @OA\Property(property="new_password_confirmation", type="string", example="new_password"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Contraseña actualizada exitosamente"),
     *     @OA\Response(response=400, description="La solicitud no pudo ser procesada"),
     *     @OA\Response(response=401, description="No tiene permisos para realizar esta accion"),
     *     @OA\Response(response=422, description="El password actual es incorrecto"),
     *     @OA\Response(response=500, description="Error interno del servidor"),
     * )
     */

     public function changePassword(ChangePasswordRequest $request)
     {
         try {
             $user = $this->authService->getAuthenticatedUser();
             $currentPassword = $request->input('current_password');
             $newPassword = $request->input('new_password');
             $this->authService->changePassword($user, $currentPassword, $newPassword);
             $this->SystemLogService->logActivity(
                'Contraseña',
                'Cambio de Contraseña',
                SeveritySystemLog::info->name,
            );
             return ApiResponse::success(SuccessMessages::PASSWORD_UPDATE_SUCCESS, [], []);
         } catch (\Exception $e) {
            $this->SystemLogService->logActivity(
                'Contraseña',
                'Cambio de Contraseña fallido',
                SeveritySystemLog::warning->name,
            );
             return ApiResponse::error($e->getMessage(), $e, [], 500);
         }
     }
}
