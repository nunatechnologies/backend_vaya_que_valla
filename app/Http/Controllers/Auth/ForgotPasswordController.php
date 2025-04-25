<?php

namespace App\Http\Controllers\Auth;

use App\Enums\SeveritySystemLog;
use App\Http\Controllers\Controller;
use App\Http\Messages\SuccessMessages;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\SystemLogService;
use App\Services\User\AuthService;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

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
     *     path="/api/change_password/{id}",
     *     summary="Change password of authenticated user",
     *     tags={"Authentication"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     security={{ "bearerAuth": {} }},
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

     public function changePassword(ChangePasswordRequest $request, $id)
     {
         try {
             $user = User::find($id);
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

    /**
     * @OA\Post(
     *     path="/api/authen/forgot-password",
     *     summary="Request link for reset password",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="usuario@correo.com")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Link enviado correctamente"),
     *     @OA\Response(response=422, description="Validación fallida"),
     *     @OA\Response(response=500, description="Error del servidor"),
     * )
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            $this->SystemLogService->logActivity(
                'Contraseña',
                'Solicitud de link de recuperación',
                SeveritySystemLog::info->name,
            );

            return ApiResponse::success('Link de recuperación enviado al correo.', [], []);
        }

        $this->SystemLogService->logActivity(
            'Contraseña',
            'Fallo al solicitar link de recuperación',
            SeveritySystemLog::warning->name,
        );

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/authen/reset-password",
     *     summary="Reset password by given token in email",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token", "email", "password", "password_confirmation"},
     *             @OA\Property(property="token", type="string", example="abc123token"),
     *             @OA\Property(property="email", type="string", format="email", example="usuario@correo.com"),
     *             @OA\Property(property="password", type="string", format="password", example="nuevaContraseñaSegura"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="nuevaContraseñaSegura")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Contraseña restablecida correctamente"),
     *     @OA\Response(response=422, description="Error de validación o token inválido"),
     *     @OA\Response(response=500, description="Error interno del servidor")
     * )
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();

                // Si tienes que guardar logs adicionales del usuario, puedes hacerlo aquí.
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $this->SystemLogService->logActivity(
                'Contraseña',
                'Restablecimiento de contraseña exitoso',
                SeveritySystemLog::info->name,
            );

            return ApiResponse::success('La contraseña ha sido restablecida correctamente.', [], []);
        }

        $this->SystemLogService->logActivity(
            'Contraseña',
            'Intento fallido de restablecimiento de contraseña',
            SeveritySystemLog::warning->name,
        );

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}
