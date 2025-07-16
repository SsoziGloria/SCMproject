<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        $analytics = [
            // Summary metrics
            'total_orders' => $this->getTotalOrders(),
            'total_revenue' => $this->getTotalRevenue(),
            'active_users' => $this->getActiveUsers(),
            'total_products' => $this->getTotalProducts(),

            // Growth metrics
            'orders_growth' => $this->getOrdersGrowth(),
            'revenue_growth' => $this->getRevenueGrowth(),
            'users_growth' => $this->getUsersGrowth(),
            'products_growth' => $this->getProductsGrowth(),

            // Chart data
            'revenue_chart' => $this->getRevenueChartData(),
            'order_status_chart' => $this->getOrderStatusChartData(),

            // Table data
            'top_products' => $this->getTopProducts(),
            'recent_orders' => $this->getRecentOrders(),
            'user_roles' => $this->getUserRoleDistribution(),
            'inventory_status' => $this->getInventoryStatus(),
        ];

        return view('admin.analytics', compact('analytics'));
    }

    private function getTotalOrders()
    {
        return Order::count();
    }

    private function getTotalRevenue()
    {
        return Order::where('payment_status', 'paid')
            ->sum('total_amount');
    }

    private function getActiveUsers()
    {
        return User::where('created_at', '>=', Carbon::now()->subMonth())
            ->count();
    }

    private function getTotalProducts()
    {
        return Product::count();
    }

    private function getOrdersGrowth()
    {
        $currentMonth = Order::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $lastMonth = Order::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();

        return $lastMonth > 0 ? round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1) : 0;
    }

    private function getRevenueGrowth()
    {
        $currentMonth = Order::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $lastMonth = Order::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        return $lastMonth > 0 ? round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1) : 0;
    }

    private function getUsersGrowth()
    {
        $currentMonth = User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $lastMonth = User::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();

        return $lastMonth > 0 ? round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1) : 0;
    }

    private function getProductsGrowth()
    {
        $currentMonth = Product::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $lastMonth = Product::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();

        return $lastMonth > 0 ? round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1) : 0;
    }

    private function getRevenueChartData()
    {
        $data = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as total')
        )
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('M d');
            })->toArray(),
            'data' => $data->pluck('total')->toArray()
        ];
    }

    private function getOrderStatusChartData()
    {
        $data = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return [
            'labels' => $data->pluck('status')->map(function ($status) {
                return ucfirst($status);
            })->toArray(),
            'data' => $data->pluck('count')->toArray()
        ];
    }

    private function getTopProducts()
    {
        // This would need an order_items table in real implementation
        return Product::with('supplier')
            ->orderBy('stock', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($product) {
                $product->total_sales = rand(10, 100);
                $product->total_revenue = $product->total_sales * $product->price;
                return $product;
            });
    }

    private function getRecentOrders()
    {
        return Order::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    private function getUserRoleDistribution()
    {
        $totalUsers = User::count();

        return User::select('role', DB::raw('COUNT(*) as count'))
            ->groupBy('role')
            ->get()
            ->map(function ($role) use ($totalUsers) {
                $role->percentage = $totalUsers > 0 ? ($role->count / $totalUsers) * 100 : 0;
                $role->growth = rand(0, 25); // Replace with actual growth calculation
                return $role;
            });
    }

    private function getInventoryStatus()
    {
        return Inventory::with('product')
            ->orderBy('quantity', 'asc')
            ->limit(10)
            ->get();
    }
}