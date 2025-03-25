<?php

namespace App\Http\Controllers;

use App\Enums\SeveritySystemLog;
use App\Http\Messages\SuccessMessages;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\Rental\RentalResource;
use App\Http\Resources\PaginacionResource;
use App\Http\Responses\ApiResponse;
use App\Services\Rental\RentalService;
use App\Services\SystemLogService;
use App\Http\Requests\Rental\RentalRequest;
use App\Http\Requests\Rental\PatchRentalRequest;

class RentalController extends Controller
{
    protected $rentalService;
    protected $systemLogService;

    public function __construct(RentalService $rentalService, SystemLogService $systemLogService)
    {
        $this->rentalService = $rentalService;
        $this->systemLogService = $systemLogService;
    }
    
    /**
     * @OA\Post(
     *     path="/api/rentals",
     *     summary="Register rental",
     *     tags={"Rentals"},
     *     @OA\RequestBody(
     *         required=true,
     *          @OA\JsonContent(
	 *             required={"user_id", "quote_id", "starts_at", "ends_at", "status", "has_lona", "total_amount", "unsubscribe_status"},
	 *                 @OA\Property(property="user_id", type="number", maxLength=20),
	 *                 @OA\Property(property="quote_id", type="number", maxLength=20),
	 *                 @OA\Property(property="starts_at", type="string"),
	 *                 @OA\Property(property="ends_at", type="string"),
	 *                 @OA\Property(property="status", type="string"),
	 *                 @OA\Property(property="has_lona", type="string", maxLength=1),
	 *                 @OA\Property(property="total_amount", type="number", maxLength=8, format="float"),
	 *                 @OA\Property(property="unsubscribe_status", type="string", maxLength=150),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Rental registered successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */

    public function register(RentalRequest $RentalRequest)
    {
        try {
            $data = $this->rentalService->createRental($RentalRequest->all());
            $this->systemLogService->logActivity('rental','Rental registrado',
                SeveritySystemLog::info->name,
                $data
            );
            return ApiResponse::success(SuccessMessages::CREATE_SUCCESS, new RentalResource($data), [], 201);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'rental',
                'Registro de Rental Fallido',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), $e, [], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/rentals/{id}",
     *     summary="Update rental",
     *     tags={"Rentals"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the rental to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         request="PatchRentalRequest",
     *         required=true,
     *         description="Updated rental data",
     *         @OA\JsonContent(
	 *             required={"user_id", "quote_id", "starts_at", "ends_at", "status", "has_lona", "total_amount", "unsubscribe_status"},
	 *                 @OA\Property(property="user_id", type="number", maxLength=20),
	 *                 @OA\Property(property="quote_id", type="number", maxLength=20),
	 *                 @OA\Property(property="starts_at", type="string"),
	 *                 @OA\Property(property="ends_at", type="string"),
	 *                 @OA\Property(property="status", type="string"),
	 *                 @OA\Property(property="has_lona", type="string", maxLength=1),
	 *                 @OA\Property(property="total_amount", type="number", maxLength=8, format="float"),
	 *                 @OA\Property(property="unsubscribe_status", type="string", maxLength=150),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Rental updated successfully"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function update_rental(PatchRentalRequest $rentalRequest, $id)
    {
        try {
            $rental = $this->rentalService->updateRental($id, $rentalRequest->validated());
            $this->systemLogService->logActivity(
                'rental',
                'Rental actualizado',
                SeveritySystemLog::info->name,
                $rental
            );
            return ApiResponse::success(SuccessMessages::UPDATE_SUCCESS, new RentalResource($rental), [], 200);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'rental',
                'Actualización de rental Fallida',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/rentals/{id}",
     *     summary="Get rental by ID",
     *     tags={"Rentals"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the rental",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rental found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Rental found"),
     *             @OA\Property(property="organization", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Rental not found"),
     *     @OA\Response(response=500, description="Internal server error"),
     * )
     */

    public function get_rental($id)
    {
        try {
            $rental = $this->rentalService->getRentalByid($id);
            return ApiResponse::success(SuccessMessages::SUCCESSFUL, new RentalResource($rental), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/rentals",
     *     summary="List rentals with pagination",
     *     tags={"Rentals"},
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
    public function list_rental_pagination(PaginationRequest $pagerequest)
    {
        try {
            $objects= $this->rentalService->getAllRentalPagination($pagerequest);
            $objects->data = RentalResource::collection($objects->getCollection());
            return ApiResponse::success(SuccessMessages::SUCCESSFUL,  new PaginacionResource($objects), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e, [], $e->getCode());
        }
    }
}
