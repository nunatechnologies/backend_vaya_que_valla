<?php

namespace App\Http\Controllers;

use App\Enums\SeveritySystemLog;
use App\Http\Messages\SuccessMessages;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\Billboard\BillboardResource;
use App\Http\Resources\PaginacionResource;
use App\Http\Responses\ApiResponse;
use App\Services\Billboard\BillboardService;
use App\Services\SystemLogService;
use App\Http\Requests\Billboard\BillboardRequest;
use App\Http\Requests\Billboard\PatchBillboardRequest;

class BillboardController extends Controller
{
    protected $billboardService;
    protected $systemLogService;

    public function __construct(BillboardService $billboardService, SystemLogService $systemLogService)
    {
        $this->billboardService = $billboardService;
        $this->systemLogService = $systemLogService;
    }
    
    /**
     * @OA\Post(
     *     path="/api/billboards",
     *     summary="Register billboard",
     *     tags={"Billboards"},
     *     @OA\RequestBody(
     *         required=true,
     *          @OA\JsonContent(
	 *             required={"name", "location", "advertiser_id", "status", "billboard_type_id", "city_id", "billboard_structure_id", "entity_status", "size", "price_per_month", "traffic_data", "image", "longitude", "latitude"},
	 *                 @OA\Property(property="name", type="string", maxLength=255),
	 *                 @OA\Property(property="location", type="string", maxLength=255),
	 *                 @OA\Property(property="advertiser_id", type="number", maxLength=20),
	 *                 @OA\Property(property="status", type="string"),
	 *                 @OA\Property(property="billboard_type_id", type="number", maxLength=20),
	 *                 @OA\Property(property="city_id", type="number", maxLength=20),
	 *                 @OA\Property(property="billboard_structure_id", type="number", maxLength=20),
	 *                 @OA\Property(property="entity_status", type="string"),
	 *                 @OA\Property(property="size", type="string", maxLength=255),
	 *                 @OA\Property(property="price_per_month", type="number", maxLength=10, format="float"),
	 *                 @OA\Property(property="traffic_data", type="string"),
	 *                 @OA\Property(property="image", type="string", maxLength=255),
	 *                 @OA\Property(property="longitude", type="number", maxLength=10, format="float"),
	 *                 @OA\Property(property="latitude", type="number", maxLength=10, format="float"),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Billboard registered successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */

    public function register(BillboardRequest $BillboardRequest)
    {
        try {
            $data = $this->billboardService->createBillboard($BillboardRequest->all());
            $this->systemLogService->logActivity('billboard','Billboard registrado',
                SeveritySystemLog::info->name,
                $data
            );
            return ApiResponse::success(SuccessMessages::CREATE_SUCCESS, new BillboardResource($data), [], 201);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'billboard',
                'Registro de Billboard Fallido',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), $e, [], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/billboards/{id}",
     *     summary="Update billboard",
     *     tags={"Billboards"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the billboard to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         request="PatchBillboardRequest",
     *         required=true,
     *         description="Updated billboard data",
     *         @OA\JsonContent(
	 *             required={"name", "location", "advertiser_id", "status", "billboard_type_id", "city_id", "billboard_structure_id", "entity_status", "size", "price_per_month", "traffic_data", "image", "longitude", "latitude"},
	 *                 @OA\Property(property="name", type="string", maxLength=255),
	 *                 @OA\Property(property="location", type="string", maxLength=255),
	 *                 @OA\Property(property="advertiser_id", type="number", maxLength=20),
	 *                 @OA\Property(property="status", type="string"),
	 *                 @OA\Property(property="billboard_type_id", type="number", maxLength=20),
	 *                 @OA\Property(property="city_id", type="number", maxLength=20),
	 *                 @OA\Property(property="billboard_structure_id", type="number", maxLength=20),
	 *                 @OA\Property(property="entity_status", type="string"),
	 *                 @OA\Property(property="size", type="string", maxLength=255),
	 *                 @OA\Property(property="price_per_month", type="number", maxLength=10, format="float"),
	 *                 @OA\Property(property="traffic_data", type="string"),
	 *                 @OA\Property(property="image", type="string", maxLength=255),
	 *                 @OA\Property(property="longitude", type="number", maxLength=10, format="float"),
	 *                 @OA\Property(property="latitude", type="number", maxLength=10, format="float"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Billboard updated successfully"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function update_billboard(PatchBillboardRequest $billboardRequest, $id)
    {
        try {
            $billboard = $this->billboardService->updateBillboard($id, $billboardRequest->validated());
            $this->systemLogService->logActivity(
                'billboard',
                'Billboard actualizado',
                SeveritySystemLog::info->name,
                $billboard
            );
            return ApiResponse::success(SuccessMessages::UPDATE_SUCCESS, new BillboardResource($billboard), [], 200);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'billboard',
                'Actualización de billboard Fallida',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/billboards/{id}",
     *     summary="Get billboard by ID",
     *     tags={"Billboards"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the billboard",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Billboard found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Billboard found"),
     *             @OA\Property(property="organization", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Billboard not found"),
     *     @OA\Response(response=500, description="Internal server error"),
     * )
     */

    public function get_billboard($id)
    {
        try {
            $billboard = $this->billboardService->getBillboardByid($id);
            return ApiResponse::success(SuccessMessages::SUCCESSFUL, new BillboardResource($billboard), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/billboards",
     *     summary="List billboards with pagination",
     *     tags={"Billboards"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search query",
     *         required=false,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="zone_id",
     *         in="query",
     *         description="Filter by zone_id",
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
    public function list_billboard_pagination(PaginationRequest $pagerequest)
    {
        try {
            $objects= $this->billboardService->getAllBillboardPagination($pagerequest);
            $objects->data = BillboardResource::collection($objects->getCollection());
            return ApiResponse::success(SuccessMessages::SUCCESSFUL,  new PaginacionResource($objects), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e, [], $e->getCode());
        }
    }
}
