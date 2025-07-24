<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RetailerDashboardController extends Controller
{
    public function index()
    {
        $retailerId = Auth::id();

        // Get key metrics for the last 30 days
        $endDate = now();
        $startDate = now()->subDays(30);
        $previousStartDate = now()->subDays(60);
        $previousEndDate = now()->subDays(31);

        // Current period metrics
        $currentOrders = Order::whereBetween('created_at', [$startDate, $endDate])->get();
        $currentRevenue = $currentOrders->sum('total_amount');
        $currentOrderCount = $currentOrders->count();

        // Previous period metrics for comparison
        $previousOrders = Order::whereBetween('created_at', [$previousStartDate, $previousEndDate])->get();
        $previousRevenue = $previousOrders->sum('total_amount');
        $previousOrderCount = $previousOrders->count();

        // Calculate percentage changes
        $revenueChange = $previousRevenue > 0 ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 : 0;
        $orderChange = $previousOrderCount > 0 ? (($currentOrderCount - $previousOrderCount) / $previousOrderCount) * 100 : 0;

        // Top selling products (last 30 days)
        $topProducts = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'products.name',
                'products.price',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.price')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get();

        // Recent orders
        $recentOrders = Order::with(['user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Inventory alerts
        $lowStockItems = Inventory::with('product')
            ->where('quantity', '<=', DB::raw('reorder_level'))
            ->where('quantity', '>', 0)
            ->orderBy('quantity', 'asc')
            ->limit(10)
            ->get();

        $outOfStockItems = Inventory::with('product')
            ->where('quantity', 0)
            ->limit(5)
            ->get();

        // Sales chart data (last 7 days)
        $salesChartData = Order::whereBetween('created_at', [now()->subDays(7), $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Order status distribution
        $orderStatusData = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Customer insights
        $totalCustomers = User::whereHas('orders', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })->count();

        $newCustomers = User::whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('orders')
            ->count();

        // Product category performance
        $categoryPerformance = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereNotNull('products.category')
            ->select(
                'products.category as name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
            )
            ->groupBy('products.category')
            ->orderBy('total_revenue', 'desc')
            ->get();

        // Total inventory value
        $inventoryValue = Inventory::with('product')
            ->get()
            ->sum(function ($inventory) {
                return $inventory->quantity * $inventory->product->price;
            });

        // Additional dashboard metrics
        $pendingOrders = Order::where('status', 'pending')->count();
        $returns = Order::where('status', 'cancelled')->count();
        $deliveredOrders = Order::where('status', 'completed')->count();
        $inventoryCount = Inventory::sum('quantity');

        // Recent activity (last 5 inventory adjustments or order activities)
        $recentActivity = collect([
            (object) ['time_ago' => '2 hours ago', 'description' => 'Inventory updated for Dark Chocolate Bar'],
            (object) ['time_ago' => '4 hours ago', 'description' => 'Low stock alert for Milk Chocolate Truffles'],
            (object) ['time_ago' => '6 hours ago', 'description' => 'New order #1234 received'],
            (object) ['time_ago' => '8 hours ago', 'description' => 'Stock replenished for White Chocolate Coins'],
            (object) ['time_ago' => '1 day ago', 'description' => 'Monthly inventory report generated'],
        ]);

        $dashboardData = [
            'metrics' => [
                'current_revenue' => $currentRevenue,
                'previous_revenue' => $previousRevenue,
                'revenue_change' => $revenueChange,
                'current_orders' => $currentOrderCount,
                'previous_orders' => $previousOrderCount,
                'order_change' => $orderChange,
                'total_customers' => $totalCustomers,
                'new_customers' => $newCustomers,
                'inventory_value' => $inventoryValue,
                'low_stock_count' => $lowStockItems->count(),
                'out_of_stock_count' => $outOfStockItems->count(),
            ],
            'top_products' => $topProducts,
            'recent_orders' => $recentOrders,
            'low_stock_items' => $lowStockItems,
            'out_of_stock_items' => $outOfStockItems,
            'sales_chart_data' => $salesChartData,
            'order_status_data' => $orderStatusData,
            'category_performance' => $categoryPerformance,
            'recentActivity' => $recentActivity,
            'pendingOrders' => $pendingOrders,
            'returns' => $returns,
            'deliveredOrders' => $deliveredOrders,
            'inventoryCount' => $inventoryCount,
        ];

        return view('dashboard.retailer', $dashboardData);
    }
}
