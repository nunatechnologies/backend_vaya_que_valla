<?php

namespace App\Http\Controllers;

use App\Enums\SeveritySystemLog;
use App\Http\Messages\SuccessMessages;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\Zone\ZoneResource;
use App\Http\Resources\PaginacionResource;
use App\Http\Responses\ApiResponse;
use App\Services\Zone\ZoneService;
use App\Services\SystemLogService;
use App\Http\Requests\Zone\ZoneRequest;
use App\Http\Requests\Zone\PatchZoneRequest;

class ZoneController extends Controller
{
    protected $zoneService;
    protected $systemLogService;

    public function __construct(ZoneService $zoneService, SystemLogService $systemLogService)
    {
        $this->zoneService = $zoneService;
        $this->systemLogService = $systemLogService;
    }
    
    /**
     * @OA\Post(
     *     path="/api/zones",
     *     summary="Register zone",
     *     tags={"Zones"},
     *     @OA\RequestBody(
     *         required=true,
     *          @OA\JsonContent(
	 *             required={"name"},
	 *                 @OA\Property(property="name", type="string", maxLength=255),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Zone registered successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */

    public function register(ZoneRequest $ZoneRequest)
    {
        try {
            $data = $this->zoneService->createZone($ZoneRequest->all());
            $this->systemLogService->logActivity('zone','Zone registrado',
                SeveritySystemLog::info->name,
                $data
            );
            return ApiResponse::success(SuccessMessages::CREATE_SUCCESS, new ZoneResource($data), [], 201);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'zone',
                'Registro de Zone Fallido',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), $e, [], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/zones/{id}",
     *     summary="Update zone",
     *     tags={"Zones"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the zone to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         request="PatchZoneRequest",
     *         required=true,
     *         description="Updated zone data",
     *         @OA\JsonContent(
	 *             required={"name"},
	 *                 @OA\Property(property="name", type="string", maxLength=255),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Zone updated successfully"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function update_zone(PatchZoneRequest $zoneRequest, $id)
    {
        try {
            $zone = $this->zoneService->updateZone($id, $zoneRequest->validated());
            $this->systemLogService->logActivity(
                'zone',
                'Zone actualizado',
                SeveritySystemLog::info->name,
                $zone
            );
            return ApiResponse::success(SuccessMessages::UPDATE_SUCCESS, new ZoneResource($zone), [], 200);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'zone',
                'Actualización de zone Fallida',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/zones/{id}",
     *     summary="Get zone by ID",
     *     tags={"Zones"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the zone",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Zone found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Zone found"),
     *             @OA\Property(property="organization", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Zone not found"),
     *     @OA\Response(response=500, description="Internal server error"),
     * )
     */

    public function get_zone($id)
    {
        try {
            $zone = $this->zoneService->getZoneByid($id);
            return ApiResponse::success(SuccessMessages::SUCCESSFUL, new ZoneResource($zone), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/zones",
     *     summary="List zones with pagination",
     *     tags={"Zones"},
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
    public function list_zone_pagination(PaginationRequest $pagerequest)
    {
        try {
            $objects= $this->zoneService->getAllZonePagination($pagerequest);
            $objects->data = ZoneResource::collection($objects->getCollection());
            return ApiResponse::success(SuccessMessages::SUCCESSFUL,  new PaginacionResource($objects), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e, [], $e->getCode());
        }
    }
}
