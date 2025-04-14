<?php

namespace App\Http\Controllers;

use App\Enums\SeveritySystemLog;
use App\Http\Messages\SuccessMessages;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\Request\RequestResource;
use App\Http\Resources\PaginacionResource;
use App\Http\Responses\ApiResponse;
use App\Services\Request\RequestService;
use App\Services\SystemLogService;
use App\Http\Requests\Request\RequestRequest;
use App\Http\Requests\Request\PatchRequestRequest;

class RequestController extends Controller
{
    protected $requestService;
    protected $systemLogService;

    public function __construct(RequestService $requestService, SystemLogService $systemLogService)
    {
        $this->requestService = $requestService;
        $this->systemLogService = $systemLogService;
    }
    
    /**
     * @OA\Post(
     *     path="/api/requests",
     *     summary="Register request",
     *     tags={"Requests"},
     *     @OA\RequestBody(
     *         required=true,
     *          @OA\JsonContent(
	 *             required={"user_id", "status"},
	 *                 @OA\Property(property="user_id", type="number", maxLength=20),
	 *                 @OA\Property(property="company", type="string", maxLength=40),
     *                 @OA\Property(property="description", type="string", maxLength=200),
     *                 @OA\Property(property="budget_description", type="string", maxLength=200),
     *                 @OA\Property(property="status", type="string", maxLength=200, description="allowed values: pending,approved,rejected"),
     *                 @OA\Property(property="tentative_start_date", type="date", example="2025-04-14"),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Request registered successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */

    public function register(RequestRequest $RequestRequest)
    {
        try {
            $data = $this->requestService->createRequest($RequestRequest->all());
            $this->systemLogService->logActivity('request','Request registrado',
                SeveritySystemLog::info->name,
                $data
            );
            return ApiResponse::success(SuccessMessages::CREATE_SUCCESS, new RequestResource($data), [], 201);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'request',
                'Registro de Request Fallido',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), $e, [], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/requests/{id}",
     *     summary="Update request",
     *     tags={"Requests"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the request to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         request="PatchRequestRequest",
     *         required=true,
     *         description="Updated request data",
     *         @OA\JsonContent(
	 *             required={"user_id", "status"},
	 *                 @OA\Property(property="user_id", type="number", maxLength=20),
	 *                 @OA\Property(property="company", type="string", maxLength=40),
     *                 @OA\Property(property="description", type="string", maxLength=200),
     *                 @OA\Property(property="budget_description", type="string", maxLength=200),
     *                 @OA\Property(property="status", type="string", maxLength=200, description="allowed values: pending,approved,rejected"),
     *                 @OA\Property(property="tentative_start_date", type="date", example="2025-04-14"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Request updated successfully"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function update_request(PatchRequestRequest $requestRequest, $id)
    {
        try {
            $request = $this->requestService->updateRequest($id, $requestRequest->validated());
            $this->systemLogService->logActivity(
                'request',
                'Request actualizado',
                SeveritySystemLog::info->name,
                $request
            );
            return ApiResponse::success(SuccessMessages::UPDATE_SUCCESS, new RequestResource($request), [], 200);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'request',
                'Actualización de request Fallida',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/requests/{id}",
     *     summary="Get request by ID",
     *     tags={"Requests"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the request",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Request found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Request found"),
     *             @OA\Property(property="organization", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Request not found"),
     *     @OA\Response(response=500, description="Internal server error"),
     * )
     */

    public function get_request($id)
    {
        try {
            $request = $this->requestService->getRequestByid($id);
            return ApiResponse::success(SuccessMessages::SUCCESSFUL, new RequestResource($request), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/requests",
     *     summary="List requests with pagination",
     *     tags={"Requests"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search query",
     *         required=false,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="Filter by user_id",
     *         required=false,
     *         @OA\Schema(type="integer", maxLength=255)
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
    public function list_request_pagination(PaginationRequest $pagerequest)
    {
        try {
            $objects= $this->requestService->getAllRequestPagination($pagerequest);
            $objects->data = RequestResource::collection($objects->getCollection());
            return ApiResponse::success(SuccessMessages::SUCCESSFUL,  new PaginacionResource($objects), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e, [], $e->getCode());
        }
    }
}
