<?php

namespace App\Http\Controllers;

use App\Enums\SeveritySystemLog;
use App\Http\Messages\SuccessMessages;
use App\Http\Requests\PaginationRequest;
use App\Http\Requests\User\PatchUserRequest;
use App\Http\Requests\User\RolRequest;
use App\Http\Requests\User\UserRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\PaginacionResource;
use App\Http\Responses\ApiResponse;
use App\Services\User\UserService;
use App\Services\SystemLogService;

class UserController extends Controller
{
    protected $userService;
    protected $systemLogService;

    public function __construct(UserService $userService, SystemLogService $systemLogService)
    {
        $this->userService = $userService;
        $this->systemLogService = $systemLogService;
    }
    
    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Register User",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *          @OA\JsonContent(
     *             required={"name", "last_name", "email","password","rol"},
     *                 @OA\Property(property="name", type="string", maxLength=255, example="Eduardo"),
     *                 @OA\Property(property="last_name", type="string", maxLength=255, example="Sanchez"),
     *                 @OA\Property(property="email", type="string", format="email", example="eduardo@gmail.com"),
     *                 @OA\Property(property="phone", type="string", maxLength=255, example="77835516"),
     *                 @OA\Property(property="cod_phone", type="string", maxLength=255, example="+591"),
     *                 @OA\Property(property="password", type="string", minLength=8),
     *                 @OA\Property(property="rol", enum={"ADMINISTRADOR", "OPERADOR", "ANUNCIANTE","AGENCIA","CLIENTE"}),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */

     public function register(UserRequest $UserRequest)
     {
         try {
             $userData = $this->userService->createUser($UserRequest->all());
             $this->systemLogService->logActivity('usuario','Usuario registrado',
                 SeveritySystemLog::info->name,
                 $userData
             );
             return ApiResponse::success(SuccessMessages::CREATE_SUCCESS, new UserResource($userData), [], 201);
         } catch (\Exception $e) {
             $this->systemLogService->logActivity(
                 'user',
                 'Registro de Usuario Fallido',
                 SeveritySystemLog::error->name,
             );
             return ApiResponse::error($e->getMessage(), $e, [], 500);
         }
     }
 
     /**
      * @OA\Post(
      *     path="/api/users/{id}/rol",
      *     summary="Update rol",
      *     tags={"Users"},
      *   @OA\Parameter(
      *         name="id",
      *         in="path",
      *         description="ID of the User",
      *         required=true,
      *         @OA\Schema(
      *             type="integer",
      *             format="int64"
      *         )
      *     ),
      *     @OA\RequestBody(
      *         required=true,
      *         @OA\JsonContent(
      *             required={ "rol"},
      *             @OA\Property(property="rol", type="string", enum={"ASESOR", "EJECUTIVO", "ADMINISTRADOR"}),
      *         )
      *     ),
      *     @OA\Response(response=200, description="Successful operation"),
      *     @OA\Response(response=400, description="Invalid request")
      * )
      */
 
     public function update_rol(RolRequest $request, $id)
     {
         try {
             $user = $this->userService->addRol($id, $request->all());
             $this->systemLogService->logActivity(
                 'rol',
                 'Rol actualizado',
                 SeveritySystemLog::info->name,
                 $user
             );
             return ApiResponse::success(SuccessMessages::CREATE_SUCCESS, new UserResource($user), [], 200);
 
         } catch (\Exception $e) {
             $this->systemLogService->logActivity(
                 'rol',
                 'Actualización de Rol fallida',
                 SeveritySystemLog::error->name,
             );
             return ApiResponse::error($e->getMessage(), $e, [], $e->getCode());
         }
     }
 
     /**
      * @OA\PUT(
      *     path="/api/users/{id}",
      *     summary="Update user",
      *     tags={"Users"},
      *     @OA\Parameter(
      *         name="id",
      *         in="path",
      *         description="ID of the user to update",
      *         required=true,
      *         @OA\Schema(type="integer")
      *     ),
      *     @OA\RequestBody(
      *         required=true,
      *         description="Updated user data",
      *         @OA\JsonContent(
      *             required={ "email", "phone" , "cod_phone"},
      *                 @OA\Property(property="name", type="string", maxLength=255, example="Eduardo"),
      *                 @OA\Property(property="last_name", type="string", maxLength=255, example="Sanchez"),
      *                 @OA\Property(property="email", type="string", format="email", example="eduardo@gmail.com"),
      *                 @OA\Property(property="phone", type="string", maxLength=255, example="77835516"),
      *                 @OA\Property(property="cod_phone", type="string", maxLength=255, example="+591"),
      *         )
      *     ),
      *     @OA\Response(response=200, description="Successful operation"),
      *     @OA\Response(response=500, description="Internal server error")
      * )
      */
 
     public function update_user(PatchUserRequest $userRequest, $id)
     {
         try {
             $user = $this->userService->updateUser($id, $userRequest->validated());
             $this->systemLogService->logActivity(
                 'user',
                 'Usuario actualizado',
                 SeveritySystemLog::info->name,
                 $user
             );
             return ApiResponse::success(SuccessMessages::UPDATE_SUCCESS, new UserResource($user), [], 200);
         } catch (\Exception $e) {
             $this->systemLogService->logActivity(
                 'user',
                 'Actualización de usuario Fallida',
                 SeveritySystemLog::error->name,
             );
             return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
         }
     }
 
     /**
      * @OA\Get(
      *     path="/api/users/{id}",
      *     summary="Get user by ID",
      *     tags={"Users"},
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
      *     @OA\Response(response=200, description="Successful operation"),
      *     @OA\Response(response=404, description="Customer not found"),
      *     @OA\Response(response=500, description="Internal server error"),
      * )
      */
 
     public function get_user($id)
     {
         try {
             $user = $this->userService->getUserByid($id);
             return ApiResponse::success(SuccessMessages::SUCCESSFUL, new UserResource($user), [], 200);
         } catch (\Exception $e) {
             return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
         }
     }

    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="List users with pagination",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search query",
     *         required=false,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="itemsPerPage",
     *         in="query",
     *         description="Items per page",
     *         required=true,
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=true,
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         description="Sort by field",
     *         required=false,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="orderBy",
     *         in="query",
     *         description="Sort order",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function list_user_pagination(PaginationRequest $pagerequest)
    {
        try {
            $objects= $this->userService->getAllUserPagination($pagerequest);
            $objects->data = UserResource::collection($objects->getCollection());
            return ApiResponse::success(SuccessMessages::SUCCESSFUL,  new PaginacionResource($objects), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e, [], $e->getCode());
        }
    }
}
