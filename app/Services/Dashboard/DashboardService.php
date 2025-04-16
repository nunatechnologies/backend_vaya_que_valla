<?php

namespace App\Services\Dashboard;

use App\Repositories\Dashboard\DashboardRepositoryInterface;

class DashboardService
{
    protected $dashboardRepository;

    public function __construct(
        DashboardRepositoryInterface $dashboardRepository,
    ) {
        $this->dashboardRepository = $dashboardRepository;
    }

    public function getKeyReports()
    {
        return [
            'total_requests_pending' => $this->dashboardRepository->countRequest('pending'),
            'total_requests_approved' => $this->dashboardRepository->countRequest('approved'),
            'total_requests_in_progress' => $this->dashboardRepository->countRequest('in_progress'),
            'total_requests_rejected' => $this->dashboardRepository->countRequest('rejected')
        ];
    }
}
