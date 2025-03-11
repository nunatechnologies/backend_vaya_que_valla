<?php

namespace App\Services;

use App\Models\SystemLog;

class SystemLogService
{
    public function logActivity(
        string $action_type,
        string $description,
        string $severity,
        $model = null
    ) {
        SystemLog::createLog([
            'action_type' => $action_type,
            'description' => $description,
            'model_type' => $model? get_class($model): null,
            'model_id' => $model ? $model->id: null,
            'severity' => $severity
        ]);
    }

    public function allSystemLog($request)
    {
        $query = SystemLog::query();

        if ($request->filled('search')) {
            $searchTerm = $request->query('search');
            $query->where(function ($query) use ($searchTerm) {
                $query->where('username', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%')
                    ->orWhere('action_type', 'like', '%' . $searchTerm . '%')
                    ->orWhere('severity', 'like', '%' . $searchTerm . '%');
            });
        }
        if ($request->has('sortBy') && $request->has('orderBy')) {
            $query->when($request->filled(['sortBy', 'orderBy']), function ($query) use ($request) {
                return $query->orderBy($request->query('sortBy'), $request->query('orderBy'));
            });
        }
        $itemsPerPage = $request->query('itemsPerPage', 10);
        $page = $request->query('page', 1);
        return $query->paginate($itemsPerPage, ['*'], 'page', $page);
    }
}
