<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Coupon;
use App\Models\CouponDownload;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function summary()
    {
        // Totales generales
        $totalUsers     = User::count();
        $activeUsers    = User::where('status', 'Activo')->count(); // Si tu users tiene "status"
        $totalCoupons   = Coupon::count();
        $totalDownloads = CouponDownload::count();

        // Top 5 cupones más descargados
        $topCoupons = Coupon::withCount('downloads')
            ->orderBy('downloads_count', 'desc')
            ->take(5)
            ->get(['id', 'title']);

        // Descargas por mes para gráfica
        $downloadsByMonth = CouponDownload::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function($item){
                return [
                    'month' => $item->month,
                    'total' => (int)$item->total
                ];
            });

        // Últimos usuarios registrados
        $latestUsers = User::orderBy('created_at', 'desc')
            ->take(5)
            ->get(['id', 'name', 'email', 'status']);

        return response()->json([
            'total_users'        => $totalUsers,
            'active_users'       => $activeUsers,
            'total_coupons'      => $totalCoupons,
            'total_downloads'    => $totalDownloads,
            'top_coupons'        => $topCoupons,
            'downloads_by_month' => $downloadsByMonth,
            'latest_users'       => $latestUsers,
        ]);
    }
}
