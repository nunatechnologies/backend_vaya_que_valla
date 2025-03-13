<?php

namespace App\Http\Controllers;

use App\Http\Messages\SuccessMessages;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\BillboardFace\BillboardFaceResource;
use App\Http\Resources\PaginacionResource;
use App\Http\Responses\ApiResponse;
use App\Models\BillboardFace;
use App\Services\BillboardFace\BillboardFaceService;
use App\Services\SystemLogService;
use Illuminate\Http\Request;

class BillboardFaceController extends Controller
{
    protected $billboardFaceService;
    protected $systemLogService;

    public function __construct(BillboardFaceService $billboardFaceService, SystemLogService $systemLogService)
    {
        $this->billboardFaceService = $billboardFaceService;
        $this->systemLogService = $systemLogService;
    }

    /**
     * @OA\Get(
     *     path="/api/billboard-faces",
     *     summary="List billboard faces with pagination",
     *     tags={"Billboard face"},
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
    public function list_billboard_face_pagination(PaginationRequest $pagerequest)
    {
        try {
            $billboardFaces = $this->billboardFaceService->getAllBillboardFacePagination($pagerequest);
            $billboardFaces->data = BillboardFaceResource::collection($billboardFaces->getCollection());
            return ApiResponse::success(SuccessMessages::SUCCESSFUL,  new PaginacionResource($billboardFaces), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e, [], $e->getCode());
        }
    }
}
