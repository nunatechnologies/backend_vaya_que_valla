<?php

namespace App\Http\Controllers;

use App\Enums\SeveritySystemLog;
use App\Http\Messages\SuccessMessages;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\Organization\OrganizationResource;
use App\Http\Resources\PaginacionResource;
use App\Http\Responses\ApiResponse;
use App\Services\Organization\OrganizationService;
use App\Services\SystemLogService;
use App\Http\Requests\Organization\OrganizationRequest;
use App\Http\Requests\Organization\PatchOrganizationRequest;

class OrganizationController extends Controller
{
    protected $organizationService;
    protected $systemLogService;

    public function __construct(OrganizationService $organizationService, SystemLogService $systemLogService)
    {
        $this->organizationService = $organizationService;
        $this->systemLogService = $systemLogService;
    }
    
    /**
     * @OA\Post(
     *     path="/api/organizations",
     *     summary="Register organization",
     *     tags={"Organizations"},
     *     @OA\RequestBody(
     *         required=true,
     *          @OA\JsonContent(
	 *             required={"user_id", "social_reason", "nit", "name_contact", "phone_contact", "commision_percentage","category_id"},
	 *                 @OA\Property(property="user_id", type="number", maxLength=20),
	 *                 @OA\Property(property="social_reason", type="string", maxLength=100),
     *                 @OA\Property(property="nit", type="string", maxLength=30),
	 *                 @OA\Property(property="name_contact", type="string", maxLength=100),
	 *                 @OA\Property(property="phone_contact", type="string", maxLength=20),
	 *                 @OA\Property(property="commision_percentage", type="number", maxLength=8, format="float"),
     *                 @OA\Property(property="category_id", type="number")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Organization registered successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */

    public function register(OrganizationRequest $OrganizationRequest)
    {
        try {
            $data = $this->organizationService->createOrganization($OrganizationRequest->all());
            $this->systemLogService->logActivity('organization','Organization registrado',
                SeveritySystemLog::info->name,
                $data
            );
            return ApiResponse::success(SuccessMessages::CREATE_SUCCESS, new OrganizationResource($data), [], 201);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'organization',
                'Registro de Organization Fallido',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), $e, [], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/organizations/{id}",
     *     summary="Update organization",
     *     tags={"Organizations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the organization to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         request="PatchOrganizationRequest",
     *         required=true,
     *         description="Updated organization data",
     *         @OA\JsonContent(
	 *             required={"social_reason", "nit"},
	 *                 @OA\Property(property="user_id", type="number", maxLength=20),
	 *                 @OA\Property(property="social_reason", type="string", maxLength=100),
	 *                 @OA\Property(property="nit", type="string", maxLength=30),
     *                 @OA\Property(property="name_contact", type="string", maxLength=100),
	 *                 @OA\Property(property="phone_contact", type="string", maxLength=20),
	 *                 @OA\Property(property="commision_percentage", type="number", maxLength=8, format="float"),
     *                 @OA\Property(property="category_id", type="number")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Organization updated successfully"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function update_organization(PatchOrganizationRequest $organizationRequest, $id)
    {
        try {
            $organization = $this->organizationService->updateOrganization($id, $organizationRequest->validated());
            $this->systemLogService->logActivity(
                'organization',
                'Organization actualizado',
                SeveritySystemLog::info->name,
                $organization
            );
            return ApiResponse::success(SuccessMessages::UPDATE_SUCCESS, new OrganizationResource($organization), [], 200);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'organization',
                'Actualización de organization Fallida',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/organizations/{id}",
     *     summary="Get organization by ID",
     *     tags={"Organizations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the organization",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Organization found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Organization found"),
     *             @OA\Property(property="organization", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Organization not found"),
     *     @OA\Response(response=500, description="Internal server error"),
     * )
     */

    public function get_organization($id)
    {
        try {
            $organization = $this->organizationService->getOrganizationByid($id);
            return ApiResponse::success(SuccessMessages::SUCCESSFUL, new OrganizationResource($organization), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/organizations",
     *     summary="List organizations with pagination",
     *     tags={"Organizations"},
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
    public function list_organization_pagination(PaginationRequest $pagerequest)
    {
        try {
            $objects= $this->organizationService->getAllOrganizationPagination($pagerequest);
            $objects->data = OrganizationResource::collection($objects->getCollection());
            return ApiResponse::success(SuccessMessages::SUCCESSFUL,  new PaginacionResource($objects), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e, [], $e->getCode());
        }
    }
}
