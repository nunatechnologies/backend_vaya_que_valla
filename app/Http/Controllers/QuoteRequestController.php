<?php

namespace App\Http\Controllers;

use App\Enums\SeveritySystemLog;
use App\Http\Messages\SuccessMessages;
use App\Http\Requests\PaginationRequest;
use App\Http\Resources\QuoteRequest\QuoteRequestResource;
use App\Http\Resources\PaginacionResource;
use App\Http\Responses\ApiResponse;
use App\Services\QuoteRequest\QuoteRequestService;
use App\Services\SystemLogService;
use App\Http\Requests\QuoteRequest\QuoteRequestRequest;
use App\Http\Requests\QuoteRequest\PatchQuoteRequestRequest;
use App\Http\Resources\Request\RequestResource;
use App\Models\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QuoteRequestController extends Controller
{
    protected $quoterequestService;
    protected $systemLogService;

    public function __construct(QuoteRequestService $quoterequestService, SystemLogService $systemLogService)
    {
        $this->quoterequestService = $quoterequestService;
        $this->systemLogService = $systemLogService;
    }
    
    /**
     * @OA\Post(
     *     path="/api/quote_request",
     *     summary="Register quoterequest",
     *     tags={"Quote_request"},
     *     @OA\RequestBody(
     *         required=true,
     *          @OA\JsonContent(
	 *             required={"request_id", "quote_id"},
	 *                 @OA\Property(property="request_id", type="number", maxLength=20),
	 *                 @OA\Property(property="quote_id", type="string", maxLength=20, description="It can be '1' for link only one quote or '1,2' to link two or more quotes"),
     *         )
     *     ),
     *     @OA\Response(response=201, description="QuoteRequest registered successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */

    public function register(QuoteRequestRequest $QuoteRequestRequest)
    {
        try {
            $request = Request::find($QuoteRequestRequest->request_id);
            $quotesIds = explode(',',$QuoteRequestRequest->quote_id);
            $request->quotes()->syncWithoutDetaching($quotesIds);

             // 1. Autentication:get token for each request
            $authUrl = 'https://crm-back.vayaquevalla.com/api/authen/login';

            $loginResponse = Http::post($authUrl, [
                'email' => 'backend@vayaquevalla.com',
                'password' => '2£0#{6I8Lea{*',
            ]);

            if (!$loginResponse->successful()) {
                Log::error('Error authenticating before to send the request', [
                    'response' => $loginResponse->body(),
                ]);
                return;
            }

            $token = $loginResponse->json('meta.accessToken');

            // 2. Send data
            $externalUrl = app()->environment('local')
                ? 'http://vayaquevalla.test/api/authen/externals/receive-request'
                : 'https://crm-back.vayaquevalla.com/api/opportunity/generate-lead-from-request';

            $response = Http::withToken($token)
                ->post($externalUrl, new RequestResource($request));

            // 3. Result after send data
            if ($response->failed()) {
                Log::error('Error pushing data to ' . $externalUrl, [
                    'response' => $response->body(),
                ]);
            } else {
                Log::info('Pushed data to ' . $externalUrl, [
                    'response' => $response->body(),
                    'data' => new RequestResource($request)
                ]);
            }

            $this->systemLogService->logActivity('quoterequest','QuoteRequest registrado',
                SeveritySystemLog::info->name,
                $request
            );
            return ApiResponse::success(SuccessMessages::CREATE_SUCCESS, new RequestResource($request), [], 201);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'quoterequest',
                'Registro de QuoteRequest Fallido',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), $e, [], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/quote_request/{id}",
     *     summary="Update quoterequest",
     *     tags={"Quote_request"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the quoterequest to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         request="PatchQuoteRequestRequest",
     *         required=true,
     *         description="Updated quoterequest data",
     *         @OA\JsonContent(
	 *             required={"request_id", "quote_id"},
	 *                 @OA\Property(property="request_id", type="number", maxLength=20),
	 *                 @OA\Property(property="quote_id", type="number", maxLength=20),
     *         )
     *     ),
     *     @OA\Response(response=200, description="QuoteRequest updated successfully"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function update_quoterequest(PatchQuoteRequestRequest $quoterequestRequest, $id)
    {
        try {
            $quoterequest = $this->quoterequestService->updateQuoteRequest($id, $quoterequestRequest->validated());
            $this->systemLogService->logActivity(
                'quoterequest',
                'QuoteRequest actualizado',
                SeveritySystemLog::info->name,
                $quoterequest
            );
            return ApiResponse::success(SuccessMessages::UPDATE_SUCCESS, new QuoteRequestResource($quoterequest), [], 200);
        } catch (\Exception $e) {
            $this->systemLogService->logActivity(
                'quoterequest',
                'Actualización de quoterequest Fallida',
                SeveritySystemLog::error->name,
            );
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/quote_request/{id}",
     *     summary="Get quoterequest by ID",
     *     tags={"Quote_request"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the quoterequest",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="QuoteRequest found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="QuoteRequest found"),
     *             @OA\Property(property="organization", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="QuoteRequest not found"),
     *     @OA\Response(response=500, description="Internal server error"),
     * )
     */

    public function get_quoterequest($id)
    {
        try {
            $quoterequest = $this->quoterequestService->getQuoteRequestByid($id);
            return ApiResponse::success(SuccessMessages::SUCCESSFUL, new QuoteRequestResource($quoterequest), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], $e->getCode());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/quote_request",
     *     summary="List quote_request with pagination",
     *     tags={"Quote_request"},
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
    public function list_quoterequest_pagination(PaginationRequest $pagerequest)
    {
        try {
            $objects= $this->quoterequestService->getAllQuoteRequestPagination($pagerequest);
            $objects->data = QuoteRequestResource::collection($objects->getCollection());
            return ApiResponse::success(SuccessMessages::SUCCESSFUL,  new PaginacionResource($objects), [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e, [], $e->getCode());
        }
    }
}
