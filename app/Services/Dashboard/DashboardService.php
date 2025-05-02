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

    public function getKeyReports($filters)
    {
        $userId = null;
        if(isset($filters['user_id']))
        {
            $userId = $filters['user_id'];
        }
        return [
            'total_requests_pending' => $this->dashboardRepository->countRequest('pending', $userId),
            'total_requests_approved' => $this->dashboardRepository->countRequest('approved', $userId),
            'total_requests_in_progress' => $this->dashboardRepository->countRequest('in_progress', $userId),
            'total_requests_rejected' => $this->dashboardRepository->countRequest('rejected', $userId)
        ];
    }
}
