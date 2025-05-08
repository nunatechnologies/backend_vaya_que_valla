<?php

namespace App\Http\Controllers;

use App\Enums\SeveritySystemLog;
use App\Http\Messages\SuccessMessages;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\BillboardStructure\BillboardStructureResource;
use App\Http\Resources\PaginacionResource;
use App\Http\Responses\ApiResponse;
use App\Services\BillboardStructure\BillboardStructureService;
use App\Services\SystemLogService;
use App\Http\Requests\BillboardStructure\BillboardStructureRequest;
use App\Http\Requests\BillboardStructure\PatchBillboardStructureRequest;

class BillboardStructureController extends Controller
{
    protected $billboardstructureService;
    protected $systemLogService;

    public function __construct(BillboardStructureService $billboardstructureService, SystemLogService $systemLogService)
    {
        $this->billboardstructureService = $billboardstructureService;
        $this->systemLogService = $systemLogService;
    }
    
    /**
     * @OA\Post(
     *     path="/api/billboard_structures",
     *     summary="Register billboardstructure",
     *     tags={"Billboard_structures"},
     *     @OA\RequestBody(
     *         required=true,
     *          @OA\JsonContent(
	 *             required={"name"},
	 *                 @OA\Property(property="name", type="string", maxLength=255),
     *         )
     *     ),
     *     @OA\Response(response=201, description="BillboardStructure registered successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */

    public function register(BillboardStructureRequest $BillboardStructureRequest)
    {
        try {
            $data = $this->billboardstructureService->createBillboardStructure($BillboardStructureRequest->all());
            $this->systemLogService->logActivity('billboardstructure','BillboardStructure registrado',
                SeveritySystemLog::info->name,
                $data
            );
            return ApiResponse::success(SuccessMessages::CREATE_SUCCESS, new BillboardStructureResource($data), [], 201);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'billboardstructure',
                'Registro de BillboardStructure Fallido',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), $e, [], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/billboard_structures/{id}",
     *     summary="Update billboardstructure",
     *     tags={"Billboard_structures"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the billboardstructure to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         request="PatchBillboardStructureRequest",
     *         required=true,
     *         description="Updated billboardstructure data",
     *         @OA\JsonContent(
	 *             required={"name"},
	 *                 @OA\Property(property="name", type="string", maxLength=255),
     *         )
     *     ),
     *     @OA\Response(response=200, description="BillboardStructure updated successfully"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function update_billboardstructure(PatchBillboardStructureRequest $billboardstructureRequest, $id)
    {
        try {
            $billboardstructure = $this->billboardstructureService->updateBillboardStructure($id, $billboardstructureRequest->validated());
            $this->systemLogService->logActivity(
                'billboardstructure',
                'BillboardStructure actualizado',
                SeveritySystemLog::info->name,
                $billboardstructure
            );
            return ApiResponse::success(SuccessMessages::UPDATE_SUCCESS, new BillboardStructureResource($billboardstructure), [], 200);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'billboardstructure',
                'Actualización de billboardstructure Fallida',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/billboard_structures/{id}",
     *     summary="Get billboardstructure by ID",
     *     tags={"Billboard_structures"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the billboardstructure",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="BillboardStructure found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="BillboardStructure found"),
     *             @OA\Property(property="organization", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="BillboardStructure not found"),
     *     @OA\Response(response=500, description="Internal server error"),
     * )
     */

    public function get_billboardstructure($id)
    {
        try {
            $billboardstructure = $this->billboardstructureService->getBillboardStructureByid($id);
            return ApiResponse::success(SuccessMessages::SUCCESSFUL, new BillboardStructureResource($billboardstructure), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/billboard_structures",
     *     summary="List billboard_structures with pagination",
     *     tags={"Billboard_structures"},
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
    public function list_billboardstructure_pagination(PaginationRequest $pagerequest)
    {
        try {
            $objects= $this->billboardstructureService->getAllBillboardStructurePagination($pagerequest);
            $objects->data = BillboardStructureResource::collection($objects->getCollection());
            return ApiResponse::success(SuccessMessages::SUCCESSFUL,  new PaginacionResource($objects), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e, [], $e->getCode());
        }
    }
}
