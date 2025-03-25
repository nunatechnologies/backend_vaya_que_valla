<?php

namespace App\Http\Controllers;

use App\Enums\SeveritySystemLog;
use App\Http\Messages\SuccessMessages;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\PaginacionResource;
use App\Http\Responses\ApiResponse;
use App\Services\Role\RoleService;
use App\Services\SystemLogService;
use App\Http\Requests\Role\RoleRequest;
use App\Http\Requests\Role\PatchRoleRequest;
use App\Http\Resources\User\RolResource;

class RoleController extends Controller
{
    protected $roleService;
    protected $systemLogService;

    public function __construct(RoleService $roleService, SystemLogService $systemLogService)
    {
        $this->roleService = $roleService;
        $this->systemLogService = $systemLogService;
    }
    
    /**
     * @OA\Post(
     *     path="/api/roles",
     *     summary="Register role",
     *     tags={"Roles"},
     *     @OA\RequestBody(
     *         required=true,
     *          @OA\JsonContent(
	 *             required={"name", "guard_name"},
	 *                 @OA\Property(property="name", type="string", maxLength=255),
	 *                 @OA\Property(property="guard_name", type="string", maxLength=255),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Role registered successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */

    public function register(RoleRequest $RoleRequest)
    {
        try {
            $data = $this->roleService->createRole($RoleRequest->all());
            $this->systemLogService->logActivity('role','Role registrado',
                SeveritySystemLog::info->name,
                $data
            );
            return ApiResponse::success(SuccessMessages::CREATE_SUCCESS, new RolResource($data), [], 201);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'role',
                'Registro de Role Fallido',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), $e, [], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/roles/{id}",
     *     summary="Update role",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the role to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         request="PatchRoleRequest",
     *         required=true,
     *         description="Updated role data",
     *         @OA\JsonContent(
	 *             required={"name", "guard_name"},
	 *                 @OA\Property(property="name", type="string", maxLength=255),
	 *                 @OA\Property(property="guard_name", type="string", maxLength=255),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Role updated successfully"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function update_role(PatchRoleRequest $roleRequest, $id)
    {
        try {
            $role = $this->roleService->updateRole($id, $roleRequest->validated());
            $this->systemLogService->logActivity(
                'role',
                'Role actualizado',
                SeveritySystemLog::info->name,
                $role
            );
            return ApiResponse::success(SuccessMessages::UPDATE_SUCCESS, new RolResource($role), [], 200);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'role',
                'Actualización de role Fallida',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/roles/{id}",
     *     summary="Get role by ID",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the role",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Role found"),
     *             @OA\Property(property="organization", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Role not found"),
     *     @OA\Response(response=500, description="Internal server error"),
     * )
     */

    public function get_role($id)
    {
        try {
            $role = $this->roleService->getRoleByid($id);
            return ApiResponse::success(SuccessMessages::SUCCESSFUL, new RolResource($role), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/roles",
     *     summary="List roles with pagination",
     *     tags={"Roles"},
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
    public function list_role_pagination(PaginationRequest $pagerequest)
    {
        try {
            $objects= $this->roleService->getAllRolePagination($pagerequest);
            $objects->data = RolResource::collection($objects->getCollection());
            return ApiResponse::success(SuccessMessages::SUCCESSFUL,  new PaginacionResource($objects), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e, [], $e->getCode());
        }
    }
}
