<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('admin_stats_dashboard', 300, function () {
            // KPIs robustos (usando COALESCE para evitar nulos)
            $totalRevenue = Order::where('status', 'completed')->selectRaw('COALESCE(SUM(total), 0) as total')->value('total');
            $totalOrders = Order::count();
            $totalUsers = User::count();
            $totalProducts = Product::count();

            // Ventas históricas completas (Agrupadas por día)
            $salesData = Order::where('status', 'completed')
                ->select(DB::raw('CAST(created_at AS DATE) as date'), DB::raw('COALESCE(SUM(total), 0) as revenue'))
                ->groupBy(DB::raw('CAST(created_at AS DATE)'))
                ->orderBy('date')
                ->get();
    
            // Usuarios históricos completos (Agrupados por día)
            $userData = User::select(DB::raw('CAST(created_at AS DATE) as date'), DB::raw('COUNT(*) as count'))
                ->groupBy(DB::raw('CAST(created_at AS DATE)'))
                ->orderBy('date')
                ->get();
    
            // Top Categorías por ventas
            $topCategories = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->select('categories.name', DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold'))
                ->groupBy('categories.id', 'categories.name')
                ->orderBy('total_sold', 'desc')
                ->take(5)
                ->get();

            return [
                'kpis' => [
                    'revenue' => (float)$totalRevenue,
                    'orders' => $totalOrders,
                    'users' => $totalUsers,
                    'products' => $totalProducts,
                    'orders_today' => Order::whereDate('created_at', Carbon::today())->count(),
                ],
                'charts' => [
                    'sales' => $salesData,
                    'users' => $userData,
                    'categories' => $topCategories
                ]
            ];
        });

        return response()->json($stats);
    }
}
