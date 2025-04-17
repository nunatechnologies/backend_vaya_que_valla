<?php

namespace App\Http\Controllers;

use App\Http\Messages\ErrorMessages;
use App\Http\Messages\SuccessMessages;
use App\Http\Requests\Dashboard\DashboardRequest;
use App\Http\Responses\ApiResponse;
use App\Services\Dashboard\DashboardService;

class DashboardController extends Controller
{
    protected $dashboardService;
    protected $dashboardSellerService;

    public function __construct(
        DashboardService $dashboardService
    ) {
        $this->dashboardService = $dashboardService;
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/general",
     *     summary="General info",
     *     tags={"Dashboard"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function getGeneralStatistics(DashboardRequest $filter)
    {
        try {
            $datos = $this->dashboardService->getKeyReports();
            return ApiResponse::success(SuccessMessages::SUCCESSFUL, $datos, [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error(ErrorMessages::BAD_REQUEST, $e->getMessage(), [], 500);
        }
    }
}
