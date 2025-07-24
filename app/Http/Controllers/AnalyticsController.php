<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Inventory;
use App\Models\InventoryAdjustment;
use App\Models\OrderStatusHistory;
use App\Services\InventoryService;
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
            'inventory_movement_chart' => $this->getInventoryMovementChart(),

            // Table data
            'top_products' => $this->getTopProducts(),
            'recent_orders' => $this->getRecentOrders(),
            'user_roles' => $this->getUserRoleDistribution(),
            'inventory_status' => $this->getInventoryStatus(),
            'recent_inventory_adjustments' => $this->getRecentInventoryAdjustments(),
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
            'data' => $data->pluck('count')->toArray(),
            'colors' => $data->pluck('status')->map(function ($status) {
                return match ($status) {
                    'pending' => '#ffc107',    // warning
                    'processing' => '#17a2b8', // info
                    'shipped' => '#007bff',    // primary
                    'delivered' => '#28a745', // success
                    'cancelled' => '#dc3545', // danger
                    default => '#6c757d'      // secondary
                };
            })->toArray()
        ];
    }

    private function getInventoryMovementChart()
    {
        // Get data for last 14 days
        $startDate = Carbon::now()->subDays(14)->startOfDay();

        $dailyData = InventoryAdjustment::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(CASE WHEN adjustment_type = "increase" THEN quantity_change ELSE 0 END) as increases'),
            DB::raw('SUM(CASE WHEN adjustment_type = "decrease" THEN ABS(quantity_change) ELSE 0 END) as decreases')
        )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $dailyData->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('M d');
            })->toArray(),
            'increases' => $dailyData->pluck('increases')->toArray(),
            'decreases' => $dailyData->pluck('decreases')->toArray()
        ];
    }

    private function getTopProducts()
    {
        // Now using actual order items to calculate top products
        return DB::table('order_items')
            ->select(
                'order_items.product_id',
                'order_items.product_name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
            )
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', 'paid')
            ->where('orders.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('order_items.product_id', 'order_items.product_name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get()
            ->map(function ($product) {
                // Get current inventory level (now consistent with product stock)
                $inventoryService = new InventoryService();
                $product->inventory_level = $inventoryService->getAvailableInventoryStock($product->product_id);

                return $product;
            });
    }

    private function getRecentOrders()
    {
        return Order::with(['user', 'items'])
            ->withCount('items')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($order) {
                $order->total_items = $order->items->sum('quantity');
                return $order;
            });
    }

    private function getUserRoleDistribution()
    {
        $totalUsers = User::count();

        return User::select('role', DB::raw('COUNT(*) as count'))
            ->groupBy('role')
            ->get()
            ->map(function ($role) use ($totalUsers) {
                $role->percentage = $totalUsers > 0 ? round(($role->count / $totalUsers) * 100, 1) : 0;

                // Calculate actual growth instead of using random values
                $currentMonth = User::where('role', $role->role)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count();

                $lastMonth = User::where('role', $role->role)
                    ->whereMonth('created_at', Carbon::now()->subMonth()->month)
                    ->whereYear('created_at', Carbon::now()->subMonth()->year)
                    ->count();

                $role->growth = $lastMonth > 0 ? round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1) : 0;
                return $role;
            });
    }

    private function getInventoryStatus()
    {
        // Group by product_id to get total quantities
        return DB::table('inventories')
            ->select(
                'inventories.product_id',
                'inventories.product_name',
                DB::raw('SUM(inventories.quantity) as total_quantity'),
                DB::raw('MAX(inventories.updated_at) as last_updated')
            )
            ->where('inventories.status', 'available')
            ->groupBy('inventories.product_id', 'inventories.product_name')
            ->orderBy('total_quantity', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                // Calculate days since last movement
                $lastAdjustment = InventoryAdjustment::whereHas('inventory', function ($query) use ($item) {
                    $query->where('product_id', $item->product_id);
                })->latest()->first();

                $item->days_since_movement = $lastAdjustment
                    ? Carbon::parse($lastAdjustment->created_at)->diffInDays(Carbon::now())
                    : null;

                // Get the reorder level from inventory
                $reorderLevel = Inventory::where('product_id', $item->product_id)
                    ->where('status', 'available')
                    ->value('reorder_level') ?? 10;

                $item->reorder_level = $reorderLevel;
                $item->status = $item->total_quantity <= 0 ? 'Out of Stock' : ($item->total_quantity <= $item->reorder_level ? 'Low Stock' : 'In Stock');

                return $item;
            });
    }

    private function getRecentInventoryAdjustments()
    {
        return InventoryAdjustment::with(['inventory', 'user', 'statusHistory.order'])
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get()
            ->map(function ($adjustment) {
                // Format data for easier display
                $adjustment->formatted_change = $adjustment->quantity_change > 0
                    ? '+' . $adjustment->quantity_change
                    : $adjustment->quantity_change;

                $adjustment->status_color = match ($adjustment->adjustment_type) {
                    'increase' => 'success',
                    'decrease' => 'danger',
                    'correction' => 'info',
                    'damage' => 'dark',
                    'expiry' => 'warning',
                    default => 'secondary'
                };

                return $adjustment;
            });
    }

    public function revenueDetails()
    {
        $analytics = [
            // Revenue metrics
            'total_revenue' => $this->getTotalRevenue(),
            'monthly_revenue' => $this->getMonthlyRevenue(),
            'daily_revenue' => $this->getDailyRevenue(),
            'revenue_growth' => $this->getRevenueGrowth(),

            // Revenue breakdowns
            'revenue_by_product' => $this->getRevenueByProduct(),
            'revenue_by_category' => $this->getRevenueByCategory(),
            'revenue_by_payment_method' => $this->getRevenueByPaymentMethod(),
            'revenue_by_status' => $this->getRevenueByStatus(),

            // Charts
            'monthly_revenue_chart' => $this->getMonthlyRevenueChart(),
            'daily_revenue_chart' => $this->getDailyRevenueChart(),
            'product_revenue_chart' => $this->getProductRevenueChart(),
        ];

        return view('admin.analytics.revenue', compact('analytics'));
    }

    public function productAnalytics()
    {
        $analytics = [
            // Product metrics
            'total_products' => $this->getTotalProducts(),
            'active_products' => $this->getActiveProducts(),
            'out_of_stock' => $this->getOutOfStockProducts(),
            'low_stock' => $this->getLowStockProducts(),

            // Product performance
            'top_selling_products' => $this->getTopSellingProducts(),
            'least_selling_products' => $this->getLeastSellingProducts(),
            'product_categories' => $this->getProductCategories(),
            'inventory_value' => $this->getInventoryValue(),

            // Charts
            'product_sales_chart' => $this->getProductSalesChart(),
            'category_distribution_chart' => $this->getCategoryDistributionChart(),
            'stock_levels_chart' => $this->getStockLevelsChart(),
        ];

        return view('admin.analytics.products', compact('analytics'));
    }

    public function userAnalytics()
    {
        $analytics = [
            // User metrics
            'total_users' => User::count(),
            'active_users' => $this->getActiveUsers(),
            'new_users_this_month' => $this->getNewUsersThisMonth(),
            'users_growth' => $this->getUsersGrowth(),

            // User engagement
            'top_customers' => $this->getTopCustomers(),
            'user_roles' => $this->getUserRoleDistribution(),
            'user_registration_trend' => $this->getUserRegistrationTrend(),
            'customer_lifetime_value' => $this->getCustomerLifetimeValue(),

            // Charts
            'user_growth_chart' => $this->getUserGrowthChart(),
            'user_role_chart' => $this->getUserRoleChart(),
            'customer_activity_chart' => $this->getCustomerActivityChart(),
        ];

        return view('admin.analytics.users', compact('analytics'));
    }

    // Revenue Details Helper Methods
    private function getMonthlyRevenue()
    {
        return Order::where('payment_status', 'paid')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');
    }

    private function getDailyRevenue()
    {
        return Order::where('payment_status', 'paid')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');
    }

    private function getRevenueByProduct()
    {
        return DB::table('order_items')
            ->select(
                'order_items.product_name',
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue'),
                DB::raw('SUM(order_items.quantity) as total_quantity')
            )
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', 'paid')
            ->groupBy('order_items.product_name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();
    }

    private function getRevenueByCategory()
    {
        return DB::table('order_items')
            ->select(
                'products.category',
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
            )
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.payment_status', 'paid')
            ->groupBy('products.category')
            ->orderByDesc('total_revenue')
            ->get();
    }

    private function getRevenueByPaymentMethod()
    {
        return Order::select(
            'payment',
            DB::raw('SUM(total_amount) as total_revenue'),
            DB::raw('COUNT(*) as order_count')
        )
            ->where('payment_status', 'paid')
            ->groupBy('payment')
            ->orderByDesc('total_revenue')
            ->get();
    }

    private function getRevenueByStatus()
    {
        return Order::select(
            'status',
            DB::raw('SUM(total_amount) as total_revenue'),
            DB::raw('COUNT(*) as order_count')
        )
            ->groupBy('status')
            ->orderByDesc('total_revenue')
            ->get();
    }

    private function getMonthlyRevenueChart()
    {
        $data = Order::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as total')
        )
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return [
            'labels' => $data->map(function ($item) {
                return Carbon::createFromDate($item->year, $item->month, 1)->format('M Y');
            })->toArray(),
            'data' => $data->pluck('total')->toArray()
        ];
    }

    private function getDailyRevenueChart()
    {
        return $this->getRevenueChartData(); // Reuse existing method
    }

    private function getProductRevenueChart()
    {
        $data = $this->getRevenueByProduct();

        return [
            'labels' => $data->pluck('product_name')->toArray(),
            'data' => $data->pluck('total_revenue')->toArray()
        ];
    }

    // Product Analytics Helper Methods
    private function getActiveProducts()
    {
        // Products are active if they have available inventory
        return DB::table('inventories')
            ->where('status', 'available')
            ->where('quantity', '>', 0)
            ->distinct('product_id')
            ->count('product_id');
    }

    private function getOutOfStockProducts()
    {
        return DB::table('inventories')
            ->select('product_id')
            ->groupBy('product_id')
            ->havingRaw('SUM(quantity) <= 0')
            ->count();
    }

    private function getLowStockProducts()
    {
        $lowStockProducts = [];
        $products = Product::all();

        foreach ($products as $product) {
            $totalStock = Inventory::where('product_id', $product->id)
                ->where('status', 'available')
                ->sum('quantity');

            // Get reorder level from inventory (using the first inventory record for the product)
            $reorderLevel = Inventory::where('product_id', $product->id)
                ->where('status', 'available')
                ->value('reorder_level') ?? 10;

            if ($totalStock > 0 && $totalStock <= $reorderLevel) {
                $lowStockProducts[] = $product;
            }
        }

        return count($lowStockProducts);
    }

    private function getTopSellingProducts()
    {
        return $this->getTopProducts(); // Reuse existing method
    }

    private function getLeastSellingProducts()
    {
        return DB::table('order_items')
            ->select(
                'order_items.product_id',
                'order_items.product_name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
            )
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', 'paid')
            ->where('orders.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('order_items.product_id', 'order_items.product_name')
            ->orderBy('total_quantity', 'asc')
            ->limit(10)
            ->get();
    }

    private function getProductCategories()
    {
        return Product::select('category', DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->orderByDesc('count')
            ->get();
    }

    private function getInventoryValue()
    {
        return DB::table('inventories')
            ->join('products', 'products.id', '=', 'inventories.product_id')
            ->where('inventories.status', 'available')
            ->sum(DB::raw('inventories.quantity * products.price'));
    }

    private function getProductSalesChart()
    {
        $data = $this->getTopSellingProducts();

        return [
            'labels' => $data->pluck('product_name')->toArray(),
            'data' => $data->pluck('total_quantity')->toArray()
        ];
    }

    private function getCategoryDistributionChart()
    {
        $data = $this->getProductCategories();

        return [
            'labels' => $data->pluck('category')->toArray(),
            'data' => $data->pluck('count')->toArray()
        ];
    }

    private function getStockLevelsChart()
    {
        $data = DB::table('inventories')
            ->select(
                'inventories.product_name',
                DB::raw('SUM(inventories.quantity) as total_stock')
            )
            ->where('inventories.status', 'available')
            ->groupBy('inventories.product_name')
            ->orderByDesc('total_stock')
            ->limit(15)
            ->get();

        return [
            'labels' => $data->pluck('product_name')->toArray(),
            'data' => $data->pluck('total_stock')->toArray()
        ];
    }

    // User Analytics Helper Methods
    private function getNewUsersThisMonth()
    {
        return User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
    }

    private function getTopCustomers()
    {
        return DB::table('orders')
            ->select(
                'users.name',
                'users.email',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.total_amount) as total_spent')
            )
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->where('orders.payment_status', 'paid')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();
    }

    private function getUserRegistrationTrend()
    {
        return User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getCustomerLifetimeValue()
    {
        return DB::table('orders')
            ->where('payment_status', 'paid')
            ->avg('total_amount');
    }

    private function getUserGrowthChart()
    {
        $data = $this->getUserRegistrationTrend();

        return [
            'labels' => $data->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('M d');
            })->toArray(),
            'data' => $data->pluck('count')->toArray()
        ];
    }

    private function getUserRoleChart()
    {
        $data = $this->getUserRoleDistribution();

        return [
            'labels' => $data->pluck('role')->map(function ($role) {
                return ucfirst($role);
            })->toArray(),
            'data' => $data->pluck('count')->toArray()
        ];
    }

    private function getCustomerActivityChart()
    {
        $data = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(DISTINCT user_id) as active_customers')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('M d');
            })->toArray(),
            'data' => $data->pluck('active_customers')->toArray()
        ];
    }
}
