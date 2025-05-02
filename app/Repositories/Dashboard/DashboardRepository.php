<?php

namespace App\Repositories\Dashboard;

use App\Models\Request;
use Illuminate\Contracts\Database\Eloquent\Builder;

class DashboardRepository implements DashboardRepositoryInterface
{
    // count requests
    public function countRequest($status, $userId = NULL)
    {
        return Request::where('status', $status)->when($userId, function(Builder $query)use($userId){
            $query->where('user_id', $userId);
        })->count();
    }
}
