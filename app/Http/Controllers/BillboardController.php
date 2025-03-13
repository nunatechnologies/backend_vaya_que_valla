<?php

namespace App\Http\Controllers;

use App\Http\Messages\SuccessMessages;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\Billboard\BillboardResource;
use App\Http\Responses\ApiResponse;
use App\Services\SystemLogService;
use App\Services\Billboard\BillboardService;
use App\Http\Resources\PaginacionResource;

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
     * @OA\Get(
     *     path="/api/billboards",
     *     summary="List billboards with pagination",
     *     tags={"Billboard"},
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
    public function list_billboard_pagination(PaginationRequest $pagerequest)
    {
        try {
            $billboards = $this->billboardService->getAllBillboardPagination($pagerequest);
            $billboards->data = BillboardResource::collection($billboards->getCollection());
            return ApiResponse::success(SuccessMessages::SUCCESSFUL,  new PaginacionResource($billboards), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e, [], $e->getCode());
        }
    }
}
