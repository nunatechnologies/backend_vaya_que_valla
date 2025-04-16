<?php

namespace App\Repositories\Dashboard;

use App\Models\Request;

class DashboardRepository implements DashboardRepositoryInterface
{
    // count requests
    public function countRequest($status)
    {
        return Request::where('status', $status)->count();
    }
}
