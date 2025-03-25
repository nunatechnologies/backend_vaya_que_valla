<?php

namespace App\Http\Controllers;

use App\Enums\SeveritySystemLog;
use App\Http\Messages\SuccessMessages;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\Quote\QuoteResource;
use App\Http\Resources\PaginacionResource;
use App\Http\Responses\ApiResponse;
use App\Services\Quote\QuoteService;
use App\Services\SystemLogService;
use App\Http\Requests\Quote\QuoteRequest;
use App\Http\Requests\Quote\PatchQuoteRequest;

class QuoteController extends Controller
{
    protected $quoteService;
    protected $systemLogService;

    public function __construct(QuoteService $quoteService, SystemLogService $systemLogService)
    {
        $this->quoteService = $quoteService;
        $this->systemLogService = $systemLogService;
    }
    
    /**
     * @OA\Post(
     *     path="/api/quotes",
     *     summary="Register quote",
     *     tags={"Quotes"},
     *     @OA\RequestBody(
     *         required=true,
     *          @OA\JsonContent(
	 *             required={"user_id", "status", "request_date", "total_amount"},
	 *                 @OA\Property(property="user_id", type="number", maxLength=20),
	 *                 @OA\Property(property="status", type="string"),
	 *                 @OA\Property(property="request_date", type="string"),
	 *                 @OA\Property(property="total_amount", type="number", maxLength=8, format="float"),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Quote registered successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */

    public function register(QuoteRequest $QuoteRequest)
    {
        try {
            $data = $this->quoteService->createQuote($QuoteRequest->all());
            $this->systemLogService->logActivity('quote','Quote registrado',
                SeveritySystemLog::info->name,
                $data
            );
            return ApiResponse::success(SuccessMessages::CREATE_SUCCESS, new QuoteResource($data), [], 201);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'quote',
                'Registro de Quote Fallido',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), $e, [], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/quotes/{id}",
     *     summary="Update quote",
     *     tags={"Quotes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the quote to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         request="PatchQuoteRequest",
     *         required=true,
     *         description="Updated quote data",
     *         @OA\JsonContent(
	 *             required={"user_id", "status", "request_date", "total_amount"},
	 *                 @OA\Property(property="user_id", type="number", maxLength=20),
	 *                 @OA\Property(property="status", type="string"),
	 *                 @OA\Property(property="request_date", type="string"),
	 *                 @OA\Property(property="total_amount", type="number", maxLength=8, format="float"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Quote updated successfully"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function update_quote(PatchQuoteRequest $quoteRequest, $id)
    {
        try {
            $quote = $this->quoteService->updateQuote($id, $quoteRequest->validated());
            $this->systemLogService->logActivity(
                'quote',
                'Quote actualizado',
                SeveritySystemLog::info->name,
                $quote
            );
            return ApiResponse::success(SuccessMessages::UPDATE_SUCCESS, new QuoteResource($quote), [], 200);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'quote',
                'Actualización de quote Fallida',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/quotes/{id}",
     *     summary="Get quote by ID",
     *     tags={"Quotes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the quote",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Quote found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Quote found"),
     *             @OA\Property(property="organization", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Quote not found"),
     *     @OA\Response(response=500, description="Internal server error"),
     * )
     */

    public function get_quote($id)
    {
        try {
            $quote = $this->quoteService->getQuoteByid($id);
            return ApiResponse::success(SuccessMessages::SUCCESSFUL, new QuoteResource($quote), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/quotes",
     *     summary="List quotes with pagination",
     *     tags={"Quotes"},
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
    public function list_quote_pagination(PaginationRequest $pagerequest)
    {
        try {
            $objects= $this->quoteService->getAllQuotePagination($pagerequest);
            $objects->data = QuoteResource::collection($objects->getCollection());
            return ApiResponse::success(SuccessMessages::SUCCESSFUL,  new PaginacionResource($objects), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e, [], $e->getCode());
        }
    }
}
