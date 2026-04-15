<?php

namespace App\Http\Controllers;

use App\Http\Messages\SuccessMessages;
use App\Http\Responses\ApiResponse;
use App\Models\BillboardFace;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProviderDashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $userId = Auth::id();
            $period = $request->query('period', 'year');
            [$from, $to] = $this->resolvePeriod($period, $request->query('from'), $request->query('to'));

            $facesByStatus = BillboardFace::forProvider($userId)
                ->selectRaw('status, COUNT(*) as count, COALESCE(SUM(price_per_month), 0) as total_price')
                ->groupBy('status')
                ->get()
                ->keyBy('status');

            $stat = function ($key) use ($facesByStatus) {
                return [
                    'count' => (int) ($facesByStatus[$key]->count ?? 0),
                    'total_price' => (float) ($facesByStatus[$key]->total_price ?? 0),
                ];
            };

            $totals = [
                'rented' => $stat('ROJO'),
                'available' => $stat('VERDE'),
                'expiring' => $stat('AMARILLO'),
                'total_count' => BillboardFace::forProvider($userId)->count(),
            ];

            $rentalHistoryQuery = Quote::query()
                ->select('quotes.*', 'billboard_faces.code as face_code', 'billboard_faces.name as face_name')
                ->join('billboard_faces', 'quotes.billboard_face_id', '=', 'billboard_faces.id')
                ->where('billboard_faces.advertiser_id', $userId)
                ->where('quotes.status', 'approved')
                ->whereBetween('quotes.start_date', [$from, $to])
                ->orderBy('quotes.start_date', 'desc');

            $rentalHistory = $rentalHistoryQuery->limit(200)->get()->map(fn($q) => [
                'id' => $q->id,
                'face_code' => $q->face_code,
                'face_name' => $q->face_name,
                'start_date' => optional($q->start_date)->format('Y-m-d'),
                'end_date' => optional($q->end_date)?->format('Y-m-d'),
                'months' => $q->months,
                'total_amount' => (float) $q->total_amount,
                'status' => $q->status,
            ]);

            $revenueByMonth = Quote::query()
                ->join('billboard_faces', 'quotes.billboard_face_id', '=', 'billboard_faces.id')
                ->where('billboard_faces.advertiser_id', $userId)
                ->where('quotes.status', 'approved')
                ->whereBetween('quotes.start_date', [$from, $to])
                ->selectRaw("DATE_FORMAT(quotes.start_date, '%Y-%m') as month, SUM(quotes.total_amount) as revenue, COUNT(*) as rentals")
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->map(fn($r) => [
                    'month' => $r->month,
                    'revenue' => (float) $r->revenue,
                    'rentals' => (int) $r->rentals,
                ]);

            return ApiResponse::success(SuccessMessages::SUCCESSFUL, [
                'period' => ['from' => $from, 'to' => $to, 'preset' => $period],
                'totals' => $totals,
                'rental_history' => $rentalHistory,
                'revenue_by_month' => $revenueByMonth,
            ], [], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), null, [], 500);
        }
    }

    private function resolvePeriod(string $preset, ?string $from, ?string $to): array
    {
        $now = Carbon::now();
        switch ($preset) {
            case 'month':
                return [$now->copy()->startOfMonth()->toDateString(), $now->copy()->endOfMonth()->toDateString()];
            case 'semester':
                return [$now->copy()->subMonths(6)->startOfDay()->toDateString(), $now->copy()->endOfDay()->toDateString()];
            case 'year':
                return [$now->copy()->startOfYear()->toDateString(), $now->copy()->endOfYear()->toDateString()];
            case 'custom':
                if ($from && $to) {
                    return [$from, $to];
                }
                // fallthrough
            default:
                return [$now->copy()->startOfYear()->toDateString(), $now->copy()->endOfYear()->toDateString()];
        }
    }
}
