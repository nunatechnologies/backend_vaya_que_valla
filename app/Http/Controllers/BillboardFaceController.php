<?php

namespace App\Http\Controllers;

use App\Enums\SeveritySystemLog;
use App\Http\Messages\SuccessMessages;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\BillboardFace\BillboardFaceResource;
use App\Http\Resources\PaginacionResource;
use App\Http\Responses\ApiResponse;
use App\Services\BillboardFace\BillboardFaceService;
use App\Services\SystemLogService;
use App\Http\Requests\BillboardFace\BillboardFaceRequest;
use App\Http\Requests\BillboardFace\PatchBillboardFaceRequest;
use Illuminate\Http\Request;

// use Illuminate\Support\Facades\Request;

class BillboardFaceController extends Controller
{
    protected $billboardfaceService;
    protected $systemLogService;

    public function __construct(BillboardFaceService $billboardfaceService, SystemLogService $systemLogService)
    {
        $this->billboardfaceService = $billboardfaceService;
        $this->systemLogService = $systemLogService;
    }
    
     /**
     * @OA\Post(
     *     path="/api/billboard_faces",
     *     summary="Register billboardface",
     *     tags={"Billboard_faces"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"code", "billboard_id", "face", "location_detail", "status"},
     *                 @OA\Property(property="code", type="string", maxLength=10),
     *                 @OA\Property(property="billboard_id", type="integer"),
     *                 @OA\Property(property="face", type="string", maxLength=10),
     *                 @OA\Property(property="location_detail", type="string", maxLength=255),
     *                 @OA\Property(property="status", type="string", enum={"ROJO", "AMARILLO", "VERDE"}),
     *                 @OA\Property(property="rented_from", type="string", description="Date format: yyyy-mm-dd"),
     *                 @OA\Property(property="available_from", type="string", description="Date format: yyyy-mm-dd"),
     *                 @OA\Property(property="image", type="string", format="binary", description="Optional image upload")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="BillboardFace registered successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */

    public function register(BillboardFaceRequest $BillboardFaceRequest)
    {
        try {
            $data = $this->billboardfaceService->createBillboardFace($BillboardFaceRequest->all());
            if (request()->hasFile('image')) 
            {
                $data
                    ->addMediaFromRequest('image')
                    ->toMediaCollection();
            }
            
            $this->systemLogService->logActivity('billboardface','BillboardFace registrado',
                SeveritySystemLog::info->name,
                $data
            );
            return ApiResponse::success(SuccessMessages::CREATE_SUCCESS, new BillboardFaceResource($data), [], 201);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'billboardface',
                'Registro de BillboardFace Fallido',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), $e, [], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/billboard_faces/{id}",
     *     summary="Update billboardface",
     *     tags={"Billboard_faces"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the billboardface to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated billboardface data",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"_method"},
     *                 @OA\Property(property="_method", type="string", default="PUT"),
     *                 @OA\Property(property="code", type="string", maxLength=10),
     *                 @OA\Property(property="billboard_id", type="integer"),
     *                 @OA\Property(property="face", type="string", maxLength=10),
     *                 @OA\Property(property="location_detail", type="string", maxLength=255),
     *                 @OA\Property(property="status", type="string", enum={"ROJO", "AMARILLO", "VERDE"}),
     *                 @OA\Property(property="rented_from", type="string", description="Date format: yyyy-mm-dd"),
     *                 @OA\Property(property="available_from", type="string", description="Date format: yyyy-mm-dd"),
     *                 @OA\Property(property="image", type="string", format="binary", description="Optional image upload")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="BillboardFace updated successfully"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function update_billboardface(PatchBillboardFaceRequest $billboardfaceRequest, $id)
    {
        try {
            $billboardface = $this->billboardfaceService->updateBillboardFace($id, $billboardfaceRequest->validated());
            if (request()->hasFile('image')) 
            {
                $billboardface
                    ->addMediaFromRequest('image')
                    ->toMediaCollection();
            }
            $this->systemLogService->logActivity(
                'billboardface',
                'BillboardFace actualizado',
                SeveritySystemLog::info->name,
                $billboardface
            );
            return ApiResponse::success(SuccessMessages::UPDATE_SUCCESS, new BillboardFaceResource($billboardface), [], 200);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'billboardface',
                'Actualización de billboardface Fallida',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/billboard_faces/{id}",
     *     summary="Get billboardface by ID",
     *     tags={"Billboard_faces"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the billboardface",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="BillboardFace found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="BillboardFace found"),
     *             @OA\Property(property="organization", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="BillboardFace not found"),
     *     @OA\Response(response=500, description="Internal server error"),
     * )
     */

    public function get_billboardface($id)
    {
        try {
            $billboardface = $this->billboardfaceService->getBillboardFaceByid($id);
            return ApiResponse::success(SuccessMessages::SUCCESSFUL, new BillboardFaceResource($billboardface), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/billboard_faces",
     *     summary="List billboard_faces with pagination",
     *     tags={"Billboard_faces"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search query",
     *         required=false,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="city_id",
     *         in="query",
     *         description="Filter by city_id",
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
    public function list_billboardface_pagination(PaginationRequest $pagerequest)
    {
        try {
            $objects= $this->billboardfaceService->getAllBillboardFacePagination($pagerequest);
            $objects->data = BillboardFaceResource::collection($objects->getCollection());
            return ApiResponse::success(SuccessMessages::SUCCESSFUL,  new PaginacionResource($objects), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e, [], $e->getCode());
        }
    }
}
