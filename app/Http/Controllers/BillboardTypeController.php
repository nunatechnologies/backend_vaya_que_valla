<?php

namespace App\Http\Controllers;

use App\Http\Messages\SuccessMessages;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\BillboardType\BillboardTypeResource;
use App\Http\Resources\PaginacionResource;
use App\Http\Responses\ApiResponse;
use App\Services\BillboardType\BillboardTypeService;
use App\Services\SystemLogService;

class BillboardTypeController extends Controller
{
    protected $billboardTypeService;
    protected $systemLogService;

    public function __construct(BillboardTypeService $billboardTypeService, SystemLogService $systemLogService)
    {
        $this->billboardTypeService = $billboardTypeService;
        $this->systemLogService = $systemLogService;
    }
    
    /**
     * @OA\Get(
     *     path="/api/billboard-types",
     *     summary="List billboard types with pagination",
     *     tags={"Billboard types"},
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
    public function list_billboard_type_pagination(PaginationRequest $pagerequest)
    {
        try {
            $billboardTypes= $this->billboardTypeService->getAllBillboardTypePagination($pagerequest);
            $billboardTypes->data = BillboardTypeResource::collection($billboardTypes->getCollection());
            return ApiResponse::success(SuccessMessages::SUCCESSFUL,  new PaginacionResource($billboardTypes), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e, [], $e->getCode());
        }
    }
}
