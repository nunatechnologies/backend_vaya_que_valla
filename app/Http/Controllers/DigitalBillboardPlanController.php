<?php

namespace App\Http\Controllers;

use App\Enums\SeveritySystemLog;
use App\Http\Messages\SuccessMessages;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\DigitalBillboardPlan\DigitalBillboardPlanResource;
use App\Http\Resources\PaginacionResource;
use App\Http\Responses\ApiResponse;
use App\Services\DigitalBillboardPlan\DigitalBillboardPlanService;
use App\Services\SystemLogService;
use App\Http\Requests\DigitalBillboardPlan\DigitalBillboardPlanRequest;
use App\Http\Requests\DigitalBillboardPlan\PatchDigitalBillboardPlanRequest;

class DigitalBillboardPlanController extends Controller
{
    protected $digitalbillboardplanService;
    protected $systemLogService;

    public function __construct(DigitalBillboardPlanService $digitalbillboardplanService, SystemLogService $systemLogService)
    {
        $this->digitalbillboardplanService = $digitalbillboardplanService;
        $this->systemLogService = $systemLogService;
    }
    
    /**
     * @OA\Post(
     *     path="/api/digital_billboard_plans",
     *     summary="Register digitalbillboardplan",
     *     tags={"Digital_billboard_plans"},
     *     @OA\RequestBody(
     *         required=true,
     *          @OA\JsonContent(
	 *             required={"name", "passes_per_hour", "description"},
	 *                 @OA\Property(property="name", type="string", maxLength=255),
	 *                 @OA\Property(property="passes_per_hour", type="string", maxLength=10),
	 *                 @OA\Property(property="description", type="string"),
     *         )
     *     ),
     *     @OA\Response(response=201, description="DigitalBillboardPlan registered successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */

    public function register(DigitalBillboardPlanRequest $DigitalBillboardPlanRequest)
    {
        try {
            $data = $this->digitalbillboardplanService->createDigitalBillboardPlan($DigitalBillboardPlanRequest->all());
            $this->systemLogService->logActivity('digitalbillboardplan','DigitalBillboardPlan registrado',
                SeveritySystemLog::info->name,
                $data
            );
            return ApiResponse::success(SuccessMessages::CREATE_SUCCESS, new DigitalBillboardPlanResource($data), [], 201);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'digitalbillboardplan',
                'Registro de DigitalBillboardPlan Fallido',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), $e, [], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/digital_billboard_plans/{id}",
     *     summary="Update digitalbillboardplan",
     *     tags={"Digital_billboard_plans"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the digitalbillboardplan to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         request="PatchDigitalBillboardPlanRequest",
     *         required=true,
     *         description="Updated digitalbillboardplan data",
     *         @OA\JsonContent(
	 *             required={"name", "passes_per_hour", "description"},
	 *                 @OA\Property(property="name", type="string", maxLength=255),
	 *                 @OA\Property(property="passes_per_hour", type="string", maxLength=10),
	 *                 @OA\Property(property="description", type="string"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="DigitalBillboardPlan updated successfully"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function update_digitalbillboardplan(PatchDigitalBillboardPlanRequest $digitalbillboardplanRequest, $id)
    {
        try {
            $digitalbillboardplan = $this->digitalbillboardplanService->updateDigitalBillboardPlan($id, $digitalbillboardplanRequest->validated());
            $this->systemLogService->logActivity(
                'digitalbillboardplan',
                'DigitalBillboardPlan actualizado',
                SeveritySystemLog::info->name,
                $digitalbillboardplan
            );
            return ApiResponse::success(SuccessMessages::UPDATE_SUCCESS, new DigitalBillboardPlanResource($digitalbillboardplan), [], 200);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'digitalbillboardplan',
                'Actualización de digitalbillboardplan Fallida',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/digital_billboard_plans/{id}",
     *     summary="Get digitalbillboardplan by ID",
     *     tags={"Digital_billboard_plans"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the digitalbillboardplan",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="DigitalBillboardPlan found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="DigitalBillboardPlan found"),
     *             @OA\Property(property="organization", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="DigitalBillboardPlan not found"),
     *     @OA\Response(response=500, description="Internal server error"),
     * )
     */

    public function get_digitalbillboardplan($id)
    {
        try {
            $digitalbillboardplan = $this->digitalbillboardplanService->getDigitalBillboardPlanByid($id);
            return ApiResponse::success(SuccessMessages::SUCCESSFUL, new DigitalBillboardPlanResource($digitalbillboardplan), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/digital_billboard_plans",
     *     summary="List digital_billboard_plans with pagination",
     *     tags={"Digital_billboard_plans"},
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
    public function list_digitalbillboardplan_pagination(PaginationRequest $pagerequest)
    {
        try {
            $objects= $this->digitalbillboardplanService->getAllDigitalBillboardPlanPagination($pagerequest);
            $objects->data = DigitalBillboardPlanResource::collection($objects->getCollection());
            return ApiResponse::success(SuccessMessages::SUCCESSFUL,  new PaginacionResource($objects), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e, [], $e->getCode());
        }
    }
}
