<?php

namespace App\Http\Controllers;

use App\Enums\SeveritySystemLog;
use App\Http\Messages\SuccessMessages;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\BillboardType\BillboardTypeResource;
use App\Http\Resources\PaginacionResource;
use App\Http\Responses\ApiResponse;
use App\Services\BillboardType\BillboardTypeService;
use App\Services\SystemLogService;
use App\Http\Requests\BillboardType\BillboardTypeRequest;
use App\Http\Requests\BillboardType\PatchBillboardTypeRequest;

class BillboardTypeController extends Controller
{
    protected $billboardtypeService;
    protected $systemLogService;

    public function __construct(BillboardTypeService $billboardtypeService, SystemLogService $systemLogService)
    {
        $this->billboardtypeService = $billboardtypeService;
        $this->systemLogService = $systemLogService;
    }
    
    /**
     * @OA\Post(
     *     path="/api/billboard_types",
     *     summary="Register billboardtype",
     *     tags={"Billboard_types"},
     *     @OA\RequestBody(
     *         required=true,
     *          @OA\JsonContent(
	 *             required={"name", "category"},
	 *                 @OA\Property(property="name", type="string", maxLength=255),
	 *                 @OA\Property(property="category", type="string"),
     *         )
     *     ),
     *     @OA\Response(response=201, description="BillboardType registered successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */

    public function register(BillboardTypeRequest $BillboardTypeRequest)
    {
        try {
            $data = $this->billboardtypeService->createBillboardType($BillboardTypeRequest->all());
            $this->systemLogService->logActivity('billboardtype','BillboardType registrado',
                SeveritySystemLog::info->name,
                $data
            );
            return ApiResponse::success(SuccessMessages::CREATE_SUCCESS, new BillboardTypeResource($data), [], 201);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'billboardtype',
                'Registro de BillboardType Fallido',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), $e, [], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/billboard_types/{id}",
     *     summary="Update billboardtype",
     *     tags={"Billboard_types"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the billboardtype to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         request="PatchBillboardTypeRequest",
     *         required=true,
     *         description="Updated billboardtype data",
     *         @OA\JsonContent(
	 *             required={"name", "category"},
	 *                 @OA\Property(property="name", type="string", maxLength=255),
	 *                 @OA\Property(property="category", type="string"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="BillboardType updated successfully"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function update_billboardtype(PatchBillboardTypeRequest $billboardtypeRequest, $id)
    {
        try {
            $billboardtype = $this->billboardtypeService->updateBillboardType($id, $billboardtypeRequest->validated());
            $this->systemLogService->logActivity(
                'billboardtype',
                'BillboardType actualizado',
                SeveritySystemLog::info->name,
                $billboardtype
            );
            return ApiResponse::success(SuccessMessages::UPDATE_SUCCESS, new BillboardTypeResource($billboardtype), [], 200);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'billboardtype',
                'Actualización de billboardtype Fallida',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/billboard_types/{id}",
     *     summary="Get billboardtype by ID",
     *     tags={"Billboard_types"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the billboardtype",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="BillboardType found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="BillboardType found"),
     *             @OA\Property(property="organization", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="BillboardType not found"),
     *     @OA\Response(response=500, description="Internal server error"),
     * )
     */

    public function get_billboardtype($id)
    {
        try {
            $billboardtype = $this->billboardtypeService->getBillboardTypeByid($id);
            return ApiResponse::success(SuccessMessages::SUCCESSFUL, new BillboardTypeResource($billboardtype), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/billboard_types",
     *     summary="List billboard_types with pagination",
     *     tags={"Billboard_types"},
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
    public function list_billboardtype_pagination(PaginationRequest $pagerequest)
    {
        try {
            $objects= $this->billboardtypeService->getAllBillboardTypePagination($pagerequest);
            $objects->data = BillboardTypeResource::collection($objects->getCollection());
            return ApiResponse::success(SuccessMessages::SUCCESSFUL,  new PaginacionResource($objects), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e, [], $e->getCode());
        }
    }
}
