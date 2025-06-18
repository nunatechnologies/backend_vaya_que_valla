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
	 *                 @OA\Property(property="department", type="string", enum={"Beni","La Paz","Santa Cruz","Cochabamba","Pando","Tarija","Chuquisaca","Oruro","Potosí"}),
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
            // Consulta para obtener todos los conteos por ciudad con sus relaciones
            // $data = DB::table('billboards')
            //     ->join('cities', 'billboards.city_id', '=', 'cities.id')
            //     ->join('provinces', 'cities.province_id', '=', 'provinces.id')
            //     ->select(
            //         'cities.name as city',
            //         'cities.department',
            //         'provinces.name as province',
            //         'cities.id as city_id',
            //         'provinces.id as province_id'
            //     )
            //     ->selectRaw('COUNT(billboards.id) as billboard_count')
            //     ->groupBy('cities.id', 'cities.name', 'cities.department', 'provinces.id', 'provinces.name')
            //     ->get();

            // // Structure results
            // $result = [];

            // foreach ($data as $row) 
            // {
            //     $department = $row->department;
            //     $province = $row->province;
            //     $city = $row->city;

            //     // Let's make sure tha department exists
            //     if (!isset($result[$department])) 
            //     {
            //         $result[$department] = [
            //             'department' => $department,
            //             'billboard_count' => 0,
            //             'provinces' => []
            //         ];
            //     }

            //     // Let's make sure that provice exists
            //     if (!isset($result[$department]['provinces'][$row->province_id])) 
            //     {
            //         $result[$department]['provinces'][$row->province_id] = [
            //             'province' => $province,
            //             'billboard_count' => 0,
            //             'cities' => []
            //         ];
            //     }

            //     // Add city
            //     $result[$department]['provinces'][$row->province_id]['cities'][] = [
            //         'city' => $city,
            //         'billboard_count' => $row->billboard_count
            //     ];

            //     // Increase count
            //     $result[$department]['provinces'][$row->province_id]['billboard_count'] += $row->billboard_count;
            //     $result[$department]['billboard_count'] += $row->billboard_count;
            // }

            // // Convertir provincias de asociativo a array
            // foreach ($result as &$dep) 
            // {
            //     $dep['provinces'] = array_values($dep['provinces']);
            // }
            $data = DB::table('billboards')
            ->join('cities', 'billboards.city_id', '=', 'cities.id')
            ->join('provinces', 'cities.province_id', '=', 'provinces.id')
            ->leftJoin('billboard_faces', 'billboards.id', '=', 'billboard_faces.billboard_id')
            ->select(
                'cities.name as city',
                'cities.department',
                'provinces.name as province',
                'cities.id as city_id',
                'provinces.id as province_id'
            )
            ->selectRaw('COUNT(DISTINCT billboards.id) as billboard_count')
            ->selectRaw('COUNT(billboard_faces.id) as billboard_face_count')
            ->groupBy('cities.id', 'cities.name', 'cities.department', 'provinces.id', 'provinces.name')
            ->get();
        
            // Estructurar resultados
            $result = [];
            
            foreach ($data as $row) 
            {
                $department = $row->department;
                $province = $row->province;
                $city = $row->city;
            
                if (!isset($result[$department])) {
                    $result[$department] = [
                        'department' => $department,
                        'billboard_count' => 0,
                        'billboard_face_count' => 0,
                        'provinces' => []
                    ];
                }
            
                if (!isset($result[$department]['provinces'][$row->province_id])) {
                    $result[$department]['provinces'][$row->province_id] = [
                        'province' => $province,
                        'billboard_count' => 0,
                        'billboard_face_count' => 0,
                        'cities' => []
                    ];
                }
            
                // Agregar ciudad
                $result[$department]['provinces'][$row->province_id]['cities'][] = [
                    'city' => $city,
                    'billboard_count' => $row->billboard_count,
                    'billboard_face_count' => $row->billboard_face_count,
                ];
            
                // Acumular totales
                $result[$department]['provinces'][$row->province_id]['billboard_count'] += $row->billboard_count;
                $result[$department]['provinces'][$row->province_id]['billboard_face_count'] += $row->billboard_face_count;
            
                $result[$department]['billboard_count'] += $row->billboard_count;
                $result[$department]['billboard_face_count'] += $row->billboard_face_count;
            }
            
            // Convertir provincias a array
            foreach ($result as &$dep) 
            {
                $dep['provinces'] = array_values($dep['provinces']);
            }

            return ApiResponse::success(SuccessMessages::SUCCESSFUL, $result, [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e, [], $e->getCode());
        }
    }
}
