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
