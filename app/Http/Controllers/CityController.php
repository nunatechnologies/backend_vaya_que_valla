<?php

namespace App\Http\Controllers;

use App\Enums\SeveritySystemLog;
use App\Http\Messages\SuccessMessages;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\City\CityResource;
use App\Http\Resources\PaginacionResource;
use App\Http\Responses\ApiResponse;
use App\Services\City\CityService;
use App\Services\SystemLogService;
use App\Http\Requests\City\CityRequest;
use App\Http\Requests\City\PatchCityRequest;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    protected $cityService;
    protected $systemLogService;

    public function __construct(CityService $cityService, SystemLogService $systemLogService)
    {
        $this->cityService = $cityService;
        $this->systemLogService = $systemLogService;
    }
    
    /**
     * @OA\Post(
     *     path="/api/cities",
     *     summary="Register city",
     *     tags={"Cities"},
     *     @OA\RequestBody(
     *         required=true,
     *          @OA\JsonContent(
	 *             required={"name", "province_id", "department"},
	 *                 @OA\Property(property="name", type="string", maxLength=255),
	 *                 @OA\Property(property="province_id", type="number", maxLength=20),
	 *                 @OA\Property(property="department", type="string"),
     *         )
     *     ),
     *     @OA\Response(response=201, description="City registered successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */

    public function register(CityRequest $CityRequest)
    {
        try {
            $data = $this->cityService->createCity($CityRequest->all());
            $this->systemLogService->logActivity('city','City registrado',
                SeveritySystemLog::info->name,
                $data
            );
            return ApiResponse::success(SuccessMessages::CREATE_SUCCESS, new CityResource($data), [], 201);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'city',
                'Registro de City Fallido',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), $e, [], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/cities/{id}",
     *     summary="Update city",
     *     tags={"Cities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the city to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         request="PatchCityRequest",
     *         required=true,
     *         description="Updated city data",
     *         @OA\JsonContent(
	 *             required={"name", "province_id", "department"},
	 *                 @OA\Property(property="name", type="string", maxLength=255),
	 *                 @OA\Property(property="province_id", type="number", maxLength=20),
	 *                 @OA\Property(property="department", type="string"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="City updated successfully"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function update_city(PatchCityRequest $cityRequest, $id)
    {
        try {
            $city = $this->cityService->updateCity($id, $cityRequest->validated());
            $this->systemLogService->logActivity(
                'city',
                'City actualizado',
                SeveritySystemLog::info->name,
                $city
            );
            return ApiResponse::success(SuccessMessages::UPDATE_SUCCESS, new CityResource($city), [], 200);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'city',
                'Actualización de city Fallida',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/cities/{id}",
     *     summary="Get city by ID",
     *     tags={"Cities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the city",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="City found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="City found"),
     *             @OA\Property(property="organization", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="City not found"),
     *     @OA\Response(response=500, description="Internal server error"),
     * )
     */

    public function get_city($id)
    {
        try {
            $city = $this->cityService->getCityByid($id);
            return ApiResponse::success(SuccessMessages::SUCCESSFUL, new CityResource($city), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/cities",
     *     summary="List cities with pagination",
     *     tags={"Cities"},
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
    public function list_city_pagination(PaginationRequest $pagerequest)
    {
        try {
            $objects= $this->cityService->getAllCityPagination($pagerequest);
            $objects->data = CityResource::collection($objects->getCollection());
            return ApiResponse::success(SuccessMessages::SUCCESSFUL,  new PaginacionResource($objects), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/cities/departments",
     *     summary="List departments with billboards quantity",
     *     tags={"Cities"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function departments()
    {
        try {
            $departments = DB::table('billboards')
                ->join('cities', 'billboards.city_id', '=', 'cities.id')
                ->select('cities.department', DB::raw('COUNT(billboards.id) as billboard_count'))
                ->groupBy('cities.department')
                ->get();

            return ApiResponse::success(SuccessMessages::SUCCESSFUL, $departments, [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e, [], $e->getCode());
        }
    }
}
