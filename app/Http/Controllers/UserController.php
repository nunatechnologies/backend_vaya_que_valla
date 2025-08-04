<?php

namespace App\Http\Controllers;

use App\Enums\SeveritySystemLog;
use App\Http\Messages\SuccessMessages;
use App\Http\Requests\PaginationRequest;
use App\Http\Requests\User\PatchUserRequest;
use App\Http\Requests\User\RolRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\UserRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\PaginacionResource;
use App\Http\Responses\ApiResponse;
use App\Services\User\UserService;
use App\Services\SystemLogService;
use App\Services\User\AuthService;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $userService;
    protected $systemLogService;
    protected $authService;

    public function __construct(
        AuthService $authService,
        UserService $userService, 
        SystemLogService $systemLogService
        )
    {
        $this->authService = $authService;
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
     *         @OA\JsonContent(
     *            required={"name", "last_name", "email","password","rol"},
     *            @OA\Property(property="name", type="string", maxLength=255, example="Eduardo"),
     *            @OA\Property(property="last_name", type="string", maxLength=255, example="Sanchez"),
     *            @OA\Property(property="email", type="string", format="email", example="eduardo@gmail.com"),
     *            @OA\Property(property="phone", type="string", maxLength=255, example="77835516"),
     *            @OA\Property(property="cod_phone", type="string", maxLength=255, example="+591"),
     *            @OA\Property(property="password", type="string", minLength=8),
     *            @OA\Property(property="rol", enum={"ADMINISTRADOR", "OPERADOR", "ANUNCIANTE","AGENCIA","CLIENTE"}, description="Allowed values: ADMINISTRADOR, OPERADOR, ANUNCIANTE, AGENCIA, CLIENTE"),
     *            @OA\Property(property="user_type", type="string", enum={"PERSON", "ORGANIZATION"}, example="PERSON"),
     *            @OA\Property(property="ci", type="string", example="12345678",description="Required if user_type is PERSON"),
     *            @OA\Property(property="social_reason", type="string", example="Mi Empresa SRL", description="Required if user_type is ORGANIZATION"),
     *            @OA\Property(property="category_id", type="number", description="Required if user_type is ORGANIZATION"),
     *            @OA\Property(property="name_contact", type="string", example="Carlos Méndez", description="Required if user_type is ORGANIZATION"),
     *            @OA\Property(property="phone_contact", type="string", example="76789987", description="Required if user_type is ORGANIZATION"),
     *            @OA\Property(property="commision_percentage", type="number", example="10", description="Required if user_type is ORGANIZATION"),
     *            @OA\Property(property="nit", type="string", description="Required if user_type is ORGANIZATION"),
     *         ),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *              required={"name", "last_name", "email","password","rol"},
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="last_name", type="string"),
     *              @OA\Property(property="email", type="string", format="email"),
     *              @OA\Property(property="cod_phone", type="string"),
     *              @OA\Property(property="phone", type="string"),
     *              @OA\Property(property="password", type="string", minLength=8),
     *              @OA\Property(property="image", type="string", format="binary", description="Optional image upload"),
     *              @OA\Property(property="rol", enum={"ADMINISTRADOR", "OPERADOR", "ANUNCIANTE","AGENCIA","CLIENTE"}, description="Allowed values: ADMINISTRADOR, OPERADOR, ANUNCIANTE, AGENCIA, CLIENTE"),
     *              @OA\Property(property="user_type", type="string", enum={"PERSON", "ORGANIZATION"}, example="PERSON"),
     *              @OA\Property(property="ci", type="string", example="12345678",description="Required if user_type is PERSON"),
     *              @OA\Property(property="social_reason", type="string", example="Mi Empresa SRL", description="Required if user_type is ORGANIZATION"),
     *              @OA\Property(property="category_id", type="number", description="Required if user_type is ORGANIZATION"),
     *              @OA\Property(property="name_contact", type="string", example="Carlos Méndez", description="Required if user_type is ORGANIZATION"),
     *              @OA\Property(property="phone_contact", type="string", example="76789987", description="Required if user_type is ORGANIZATION"),
     *              @OA\Property(property="commision_percentage", type="number", example="10", description="Required if user_type is ORGANIZATION"),
     *              @OA\Property(property="nit", type="string", description="Required if user_type is ORGANIZATION"),
     *            )
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
            $userData->sendEmailVerificationNotification();
            if (request()->hasFile('image')) 
            {
                $userData->addMediaFromRequest('image')->toMediaCollection();
            }
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
      *             @OA\Property(property="rol", type="string", enum={"ADMINISTRADOR", "OPERADOR", "ANUNCIANTE","AGENCIA","CLIENTE"}),
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
      *             required={"name", "last_name", "cod_phone","phone"},
      *                 @OA\Property(property="name", type="string", maxLength=255, example="Eduardo"),
      *                 @OA\Property(property="last_name", type="string", maxLength=255, example="Sanchez"),
      *                 @OA\Property(property="email", type="string", format="email", example="eduardo@gmail.com"),
      *                 @OA\Property(property="phone", type="string", maxLength=255, example="77835516"),
      *                 @OA\Property(property="cod_phone", type="string", maxLength=255, example="+591"),
      *                 @OA\Property(property="user_type", type="string", enum={"PERSON", "ORGANIZATION"}, example="PERSON"),
      *                 @OA\Property(property="entity_status", type="string", enum={"active", "inactive"}, example="active"),
      *                 @OA\Property(property="ci", type="string", example="12345678",description="Required if user_type is PERSON"),
      *                 @OA\Property(property="social_reason", type="string", example="Mi Empresa SRL", description="Required if user_type is ORGANIZATION"),
      *                 @OA\Property(property="category_id", type="number", description="Required if user_type is ORGANIZATION"),
      *                 @OA\Property(property="name_contact", type="string", example="Carlos Méndez", description="Required if user_type is ORGANIZATION"),
      *                 @OA\Property(property="phone_contact", type="string", example="76789987", description="Required if user_type is ORGANIZATION"),
      *                 @OA\Property(property="commision_percentage", type="number", example="10", description="Required if user_type is ORGANIZATION"),
      *                 @OA\Property(property="nit", type="string", description="Required if user_type is ORGANIZATION"),
      *         ),
      *        @OA\MediaType(
      *            mediaType="multipart/form-data",
      *            @OA\Schema(
      *                required={"name", "last_name", "cod_phone","phone"},
      *                @OA\Property(property="_method", type="string", default="PUT"),
      *                @OA\Property(property="name", type="string"),
      *                @OA\Property(property="last_name", type="string"),
      *                @OA\Property(property="email", type="string", format="email", example="eduardo@gmail.com"), 
      *                @OA\Property(property="cod_phone", type="string"),
      *                @OA\Property(property="phone", type="string"),
      *                @OA\Property(property="image", type="string", format="binary", description="Optional image upload"),
      *                @OA\Property(property="entity_status", type="string", enum={"active", "inactive"}, example="active"),
      *                @OA\Property(property="ci", type="string", example="12345678",description="Required if user_type is PERSON"),
      *                @OA\Property(property="social_reason", type="string", example="Mi Empresa SRL", description="Required if user_type is ORGANIZATION"),
      *                @OA\Property(property="category_id", type="number", description="Required if user_type is ORGANIZATION"),
      *                @OA\Property(property="name_contact", type="string", example="Carlos Méndez", description="Required if user_type is ORGANIZATION"),
      *                @OA\Property(property="phone_contact", type="string", example="76789987", description="Required if user_type is ORGANIZATION"),
      *                @OA\Property(property="commision_percentage", type="number", example="10", description="Required if user_type is ORGANIZATION"),
      *                @OA\Property(property="nit", type="string", description="Required if user_type is ORGANIZATION"),
      *             )
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
            if (request()->hasFile('image')) 
            {
                $user->addMediaFromRequest('image')->toMediaCollection();
            }
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
     *     @OA\Parameter(
     *         name="role",
     *         in="query",
     *         description="Filter by role",
     *         required=false,
     *         @OA\Schema(type="string", enum={"ADMINISTRADOR", "OPERADOR", "ANUNCIANTE","AGENCIA","CLIENTE"})
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

    /**
     * @OA\Post(
     *     path="/api/users/update_profile",
     *     summary="Update profile of authenticated user",
     *     tags={"Users"},
     *   security={{ "bearerAuth": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Update user profile",
     *         @OA\JsonContent(
     *             required={"_method","name", "last_name", "cod_phone","phone"},
     *             @OA\Property(property="_method", type="string", default="PUT"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="last_name", type="string"),
     *             @OA\Property(property="cod_phone", type="string"),
     *             @OA\Property(property="phone", type="string")
     *         ),
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"_method"},
     *                 required={"name", "last_name", "cod_phone","phone"},
     *                 @OA\Property(property="_method", type="string", default="PUT"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="last_name", type="string"),
     *                 @OA\Property(property="cod_phone", type="string"),
     *                 @OA\Property(property="phone", type="string"),
     *                 @OA\Property(property="image", type="string", format="binary", description="Optional image upload")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="The request was successful."),
     *     @OA\Response(response=400, description="The server could not understand the request due to invalid syntax."),
     *     @OA\Response(response=401, description="Authentication is required or has failed."),
     *     @OA\Response(response=422, description="The server understands the content type and syntax, but the request was semantically invalid (e.g. validation error)."),
     *     @OA\Response(response=500, description="The server encountered an unexpected condition that prevented it from fulfilling the request."),
     * )
     */

     public function update_profile(UpdateProfileRequest $request)
     {
        try {
            
            $user = $this->authService->getAuthenticatedUser();
            
            $user = $this->userService->updateUser($user->id, $request->validated());
            if (request()->hasFile('image')) 
            {
                $user->addMediaFromRequest('image')->toMediaCollection();
            }
            $this->systemLogService->logActivity(
               'Profile',
               'Update profile',
               SeveritySystemLog::info->name,
            );
            return ApiResponse::success(SuccessMessages::PROFILE_UPDATE_SUCCESS, [], []);
        } catch (\Exception $e) {
           $this->systemLogService->logActivity(
               'Profile',
               'Failed trying to update profile',
               SeveritySystemLog::warning->name,
           );
            return ApiResponse::error($e->getMessage(), $e, [], 500);
        }
    }
}