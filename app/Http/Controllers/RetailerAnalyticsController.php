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

class RetailerAnalyticsController extends Controller
{
    public function index()
    {
        $retailerId = Auth::id();

        // Get key metrics for the last 30 days
        $endDate = now();
        $startDate = now()->subDays(30);

        $metrics = $this->getKeyMetrics($retailerId, $startDate, $endDate);
        $chartData = $this->getChartData($retailerId, $startDate, $endDate);

        return view('retailer.analytics.index', compact('metrics', 'chartData'));
    }

    public function sales(Request $request)
    {
        $retailerId = Auth::id();

        // Handle date filtering
        $dateRange = $this->getDateRange($request);
        $startDate = $dateRange['startDate'];
        $endDate = $dateRange['endDate'];
        $selectedPeriod = $dateRange['selectedPeriod'];

        // Get sales analytics
        $salesData = $this->getSalesAnalytics($retailerId, $startDate, $endDate);

        return view('retailer.analytics.sales', compact('salesData', 'selectedPeriod'));
    }

    public function inventory(Request $request)
    {
        $retailerId = Auth::id();

        $inventoryData = $this->getInventoryAnalytics($retailerId);

        return view('retailer.analytics.inventory', compact('inventoryData'));
    }

    public function customers(Request $request)
    {
        $retailerId = Auth::id();

        // Handle date filtering
        $dateRange = $this->getDateRange($request);
        $startDate = $dateRange['startDate'];
        $endDate = $dateRange['endDate'];
        $selectedPeriod = $dateRange['selectedPeriod'];

        $customerData = $this->getCustomerAnalytics($retailerId, $startDate, $endDate);

        return view('retailer.analytics.customers', compact('customerData', 'selectedPeriod'));
    }

    private function getDateRange($request)
    {
        $selectedPeriod = $request->get('period', '30_days');

        switch ($selectedPeriod) {
            case '7_days':
                $startDate = now()->subDays(7);
                break;
            case '30_days':
                $startDate = now()->subDays(30);
                break;
            case '90_days':
                $startDate = now()->subDays(90);
                break;
            case 'custom':
                $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : now()->subDays(30);
                $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : now();
                return [
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'selectedPeriod' => $selectedPeriod
                ];
            default:
                $startDate = now()->subDays(30);
        }

        $endDate = now();

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedPeriod' => $selectedPeriod
        ];
    }

    private function getKeyMetrics($retailerId, $startDate, $endDate)
    {
        // Calculate previous period for comparison
        $periodDays = $startDate->diffInDays($endDate);
        $previousStartDate = $startDate->copy()->subDays($periodDays);
        $previousEndDate = $startDate->copy()->subDay();

        // Current period metrics
        $currentOrders = Order::whereBetween('created_at', [$startDate, $endDate])->get();
        $currentRevenue = $currentOrders->where('status', 'delivered')->sum('total_amount');
        $currentOrderCount = $currentOrders->count();
        $currentAvgOrderValue = $currentOrderCount > 0 ? $currentRevenue / $currentOrderCount : 0;

        // Previous period metrics
        $previousOrders = Order::whereBetween('created_at', [$previousStartDate, $previousEndDate])->get();
        $previousRevenue = $previousOrders->where('status', 'delivered')->sum('total_amount');
        $previousOrderCount = $previousOrders->count();
        $previousAvgOrderValue = $previousOrderCount > 0 ? $previousRevenue / $previousOrderCount : 0;

        // Calculate percentage changes
        $revenueChange = $previousRevenue > 0 ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 : 0;
        $orderChange = $previousOrderCount > 0 ? (($currentOrderCount - $previousOrderCount) / $previousOrderCount) * 100 : 0;
        $avgOrderChange = $previousAvgOrderValue > 0 ? (($currentAvgOrderValue - $previousAvgOrderValue) / $previousAvgOrderValue) * 100 : 0;

        // Additional metrics
        $totalCustomers = User::whereHas('orders', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })->count();

        $lowStockItems = Inventory::where('quantity', '<=', DB::raw('reorder_level'))->count();

        return [
            'revenue' => [
                'current' => $currentRevenue,
                'previous' => $previousRevenue,
                'change' => $revenueChange,
            ],
            'orders' => [
                'current' => $currentOrderCount,
                'previous' => $previousOrderCount,
                'change' => $orderChange,
            ],
            'avg_order_value' => [
                'current' => $currentAvgOrderValue,
                'previous' => $previousAvgOrderValue,
                'change' => $avgOrderChange,
            ],
            'customers' => $totalCustomers,
            'low_stock_items' => $lowStockItems,
        ];
    }

    private function getChartData($retailerId, $startDate, $endDate)
    {
        // Daily sales chart data
        $dailySales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Product category sales
        $categorySales = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
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

        return [
            'daily_sales' => $dailySales,
            'category_sales' => $categorySales,
        ];
    }

    private function getSalesAnalytics($retailerId, $startDate, $endDate)
    {
        // Top selling products
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
            ->limit(10)
            ->get();

        // Sales by status
        $ordersByStatus = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as revenue'))
            ->groupBy('status')
            ->get();

        // Hourly sales pattern
        $hourlySales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('AVG(total_amount) as avg_revenue')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return [
            'top_products' => $topProducts,
            'orders_by_status' => $ordersByStatus,
            'hourly_sales' => $hourlySales,
        ];
    }

    private function getInventoryAnalytics($retailerId)
    {
        $inventories = Inventory::with('product')->get();

        // Stock levels analysis
        $stockLevels = [
            'in_stock' => $inventories->where('quantity', '>', 0)->count(),
            'low_stock' => $inventories->filter(function ($inventory) {
                return $inventory->quantity > 0 && $inventory->quantity <= $inventory->reorder_level;
            })->count(),
            'out_of_stock' => $inventories->where('quantity', 0)->count(),
        ];

        // Top value inventory items
        $topValueItems = $inventories->map(function ($inventory) {
            return [
                'product_name' => $inventory->product->name,
                'quantity' => $inventory->quantity,
                'unit_price' => $inventory->product->price,
                'total_value' => $inventory->quantity * $inventory->product->price,
                'status' => $inventory->quantity <= $inventory->reorder_level ? 'low' : 'normal',
            ];
        })->sortByDesc('total_value')->take(20);

        // Inventory turnover (simplified - based on recent sales)
        $thirtyDaysAgo = now()->subDays(30);
        $turnoverData = Product::with('orderItems.order')
            ->get()
            ->map(function ($product) use ($thirtyDaysAgo) {
                $soldQuantity = $product->orderItems()
                    ->whereHas('order', function ($query) use ($thirtyDaysAgo) {
                        $query->where('created_at', '>=', $thirtyDaysAgo);
                    })
                    ->sum('quantity');

                $currentStock = $product->inventory ? $product->inventory->quantity : 0;
                $turnoverRate = $currentStock > 0 ? $soldQuantity / $currentStock : 0;

                return [
                    'product_name' => $product->name,
                    'current_stock' => $currentStock,
                    'sold_quantity' => $soldQuantity,
                    'turnover_rate' => $turnoverRate,
                ];
            })->sortByDesc('turnover_rate')->take(15);

        return [
            'stock_levels' => $stockLevels,
            'top_value_items' => $topValueItems,
            'turnover_data' => $turnoverData,
            'total_inventory_value' => $inventories->sum(function ($inventory) {
                return $inventory->quantity * $inventory->product->price;
            }),
        ];
    }

    private function getCustomerAnalytics($retailerId, $startDate, $endDate)
    {
        // Customer lifetime value analysis
        $customers = User::whereHas('orders', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })->with(['orders' => function ($query) {
            $query->orderBy('created_at');
        }])->get();

        $customerAnalysis = $customers->map(function ($customer) {
            $totalSpent = $customer->orders->where('status', 'delivered')->sum('total_amount');
            $orderCount = $customer->orders->count();
            $firstOrder = $customer->orders->first();
            $lastOrder = $customer->orders->last();

            $customerLifetime = $firstOrder && $lastOrder
                ? $firstOrder->created_at->diffInDays($lastOrder->created_at) + 1
                : 1;

            return [
                'name' => $customer->name,
                'email' => $customer->email,
                'total_spent' => $totalSpent,
                'order_count' => $orderCount,
                'avg_order_value' => $orderCount > 0 ? $totalSpent / $orderCount : 0,
                'customer_lifetime_days' => $customerLifetime,
                'first_order_date' => $firstOrder ? $firstOrder->created_at : null,
                'last_order_date' => $lastOrder ? $lastOrder->created_at : null,
            ];
        })->sortByDesc('total_spent');

        // New vs returning customers
        $newCustomers = User::whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('orders')
            ->count();

        $returningCustomers = $customers->count() - $newCustomers;

        // Customer segments by order value
        $customerSegments = [
            'high_value' => $customerAnalysis->where('total_spent', '>=', 500000)->count(), // UGX 500k+
            'medium_value' => $customerAnalysis->whereBetween('total_spent', [100000, 499999])->count(), // UGX 100k-500k
            'low_value' => $customerAnalysis->where('total_spent', '<', 100000)->count(), // < UGX 100k
        ];

        return [
            'customer_analysis' => $customerAnalysis->take(20),
            'new_customers' => $newCustomers,
            'returning_customers' => $returningCustomers,
            'customer_segments' => $customerSegments,
            'total_customers' => $customers->count(),
        ];
    }
}
