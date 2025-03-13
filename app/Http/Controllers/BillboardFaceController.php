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
