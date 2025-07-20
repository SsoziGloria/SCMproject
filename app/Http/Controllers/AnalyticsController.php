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
                // Get current inventory level
                $product->inventory_level = Inventory::where('product_id', $product->product_id)
                    ->where('status', 'available')
                    ->sum('quantity');

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

                // Get the reorder level from the product
                $product = Product::find($item->product_id);
                $item->reorder_level = $product->reorder_level ?? 10;
                $item->status = $item->total_quantity <= 0 ? 'Out of Stock' :
                    ($item->total_quantity <= $item->reorder_level ? 'Low Stock' : 'In Stock');

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
}