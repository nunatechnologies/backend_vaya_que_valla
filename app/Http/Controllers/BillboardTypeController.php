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

    public function get_billboardtype($id)
    {
        try {
            $billboardtype = $this->billboardtypeService->getBillboardTypeByid($id);
            return ApiResponse::success(SuccessMessages::SUCCESSFUL, new BillboardTypeResource($billboardtype), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

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
