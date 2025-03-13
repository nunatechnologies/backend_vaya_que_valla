<?php

namespace App\Http\Controllers\Auth;

use App\Enums\SeveritySystemLog;
use App\Http\Controllers\Controller;
use App\Http\Messages\SuccessMessages;
use App\Http\Requests\User\AuthRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\SystemLog;
use App\Services\SystemLogService;
use App\Services\User\AuthService;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    protected $authService;
    protected $SystemLogService;

    public function __construct(
        AuthService $authService,
        SystemLogService $SystemLogService
    ) {
        $this->authService = $authService;
        $this->SystemLogService = $SystemLogService;
        $this->middleware('jwt', ['except' => [
            'login',
            'logout'
        ]]);
    }

    /**
     * @OA\Post(
     *     path="/api/authen/login",
     *     summary="Login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="user1@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="12345678"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Succes login"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    protected function login(AuthRequest $authrequest)
    {
        try {
            $user = $this->authService->authUser($authrequest->input('email'), $authrequest->input('password'));
            Auth::login($user);
            $token = JWTAuth::fromUser($user);
            $tokenTTL = auth('api')->factory()->getTTL();
            $tokenExpiration = now()->addMinutes($tokenTTL);
            $issuedAt = now();
            $this->SystemLogService->logActivity(
                'login',
                'Inicio de sesión',
                SeveritySystemLog::info->name,
            );
            return ApiResponse::success(
                SuccessMessages::LOGIN_SUCCESS,
                new UserResource($user),
                [
                    'accessToken' => $token,
                    'tokenExpiration' => $tokenExpiration->toIso8601String(),
                    'issuedAt' => $issuedAt->toIso8601String(),
                ]
            );
        } catch (\Exception $e) {

            $this->SystemLogService->logActivity(
                'login_failed',
                'Intento de inicio de sesión fallido',
                SeveritySystemLog::warning->name,
            );
            return ApiResponse::error($e->getMessage(), null, [], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/authen/logout",
     *     summary="Logout",
     *     tags={"Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Succes logout"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return ApiResponse::success(SuccessMessages::LOGOUT_SUCCESS, [], [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al intentar hacer logout', null, [], 500);
        }
    }
}
