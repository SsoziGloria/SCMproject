<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RetailerSalesController extends Controller
{
    public function index(Request $request)
    {
        // Get retailer's store ID
        $retailerId = Auth::user()->id;

        // Handle date filtering
        $dateRange = $this->getDateRange($request);
        $startDate = $dateRange['startDate'];
        $endDate = $dateRange['endDate'];
        $previousStartDate = $dateRange['previousStartDate'];
        $previousEndDate = $dateRange['previousEndDate'];
        $selectedPeriod = $dateRange['selectedPeriod'];

        // Get key metrics
        $metrics = $this->getKeyMetrics($retailerId, $startDate, $endDate, $previousStartDate, $previousEndDate);

        // Get sales chart data
        $salesData = $this->getSalesChartData($retailerId, $startDate, $endDate);

        // Get top products
        $topProducts = $this->getTopProducts($retailerId, $startDate, $endDate);

        // Get recent orders
        $recentOrders = $this->getRecentOrders($retailerId);

        // Get category sales data
        $categoryData = $this->getCategorySalesData($retailerId, $startDate, $endDate);

        return view('retailer.sales', compact(
            'metrics',
            'salesData',
            'topProducts',
            'recentOrders',
            'categoryData',
            'selectedPeriod'
        ));
    }

    private function getDateRange(Request $request)
    {
        $period = $request->input('period', 'month');
        $today = Carbon::today();

        // Custom date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
            $dayDiff = $startDate->diffInDays($endDate);

            $previousStartDate = (clone $startDate)->subDays($dayDiff + 1);
            $previousEndDate = (clone $startDate)->subDays(1);

            return [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'previousStartDate' => $previousStartDate,
                'previousEndDate' => $previousEndDate,
                'selectedPeriod' => $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y')
            ];
        }

        // Predefined periods
        switch ($period) {
            case 'today':
                $startDate = $today->copy()->startOfDay();
                $endDate = $today->copy()->endOfDay();
                $previousStartDate = $today->copy()->subDay()->startOfDay();
                $previousEndDate = $today->copy()->subDay()->endOfDay();
                $selectedPeriod = 'Today';
                break;

            case 'week':
                $startDate = $today->copy()->startOfWeek();
                $endDate = $today->copy()->endOfDay();
                $previousStartDate = $today->copy()->subWeek()->startOfWeek();
                $previousEndDate = $today->copy()->subWeek()->endOfWeek();
                $selectedPeriod = 'This Week';
                break;

            case 'quarter':
                $startDate = $today->copy()->startOfQuarter();
                $endDate = $today->copy()->endOfDay();
                $previousStartDate = $today->copy()->subQuarter()->startOfQuarter();
                $previousEndDate = $today->copy()->subQuarter()->endOfQuarter();
                $selectedPeriod = 'This Quarter';
                break;

            case 'year':
                $startDate = $today->copy()->startOfYear();
                $endDate = $today->copy()->endOfDay();
                $previousStartDate = $today->copy()->subYear()->startOfYear();
                $previousEndDate = $today->copy()->subYear()->endOfYear();
                $selectedPeriod = 'This Year';
                break;

            case 'month':
            default:
                $startDate = $today->copy()->subDays(30)->startOfDay();
                $endDate = $today->copy()->endOfDay();
                $previousStartDate = $today->copy()->subDays(60)->startOfDay();
                $previousEndDate = $today->copy()->subDays(31)->endOfDay();
                $selectedPeriod = 'Last 30 days';
                break;
        }

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'previousStartDate' => $previousStartDate,
            'previousEndDate' => $previousEndDate,
            'selectedPeriod' => $selectedPeriod
        ];
    }

    private function getKeyMetrics($retailerId, $startDate, $endDate, $previousStartDate, $previousEndDate)
    {
        // Current period metrics
        $currentOrders = Order::where('user_id', $retailerId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalSales = $currentOrders->sum('total_amount');
        $orderCount = $currentOrders->count();
        $averageOrder = $orderCount > 0 ? $totalSales / $orderCount : 0;

        // Calculate profit
        $profit = 0;
        foreach ($currentOrders as $order) {
            foreach ($order->items as $item) {
                $profit += ($item->price - ($item->unit_cost ?? 0)) * $item->quantity;
            }
        }

        // Previous period metrics for comparison
        $previousOrders = Order::where('user_id', $retailerId)
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->get();

        $previousTotalSales = $previousOrders->sum('total_amount');
        $previousOrderCount = $previousOrders->count();
        $previousAverageOrder = $previousOrderCount > 0 ? $previousTotalSales / $previousOrderCount : 0;

        // Calculate previous profit
        $previousProfit = 0;
        foreach ($previousOrders as $order) {
            foreach ($order->items as $item) {
                $previousProfit += ($item->price - ($item->unit_cost ?? 0)) * $item->quantity;
            }
        }

        // Calculate growth percentages
        $salesGrowth = $previousTotalSales > 0 ? (($totalSales - $previousTotalSales) / $previousTotalSales) * 100 : 0;
        $orderGrowth = $previousOrderCount > 0 ? (($orderCount - $previousOrderCount) / $previousOrderCount) * 100 : 0;
        $aovGrowth = $previousAverageOrder > 0 ? (($averageOrder - $previousAverageOrder) / $previousAverageOrder) * 100 : 0;
        $profitGrowth = $previousProfit > 0 ? (($profit - $previousProfit) / $previousProfit) * 100 : 0;

        return [
            'totalSales' => $totalSales,
            'orderCount' => $orderCount,
            'averageOrder' => $averageOrder,
            'profit' => $profit,
            'salesGrowth' => round($salesGrowth, 1),
            'orderGrowth' => round($orderGrowth, 1),
            'aovGrowth' => round($aovGrowth, 1),
            'profitGrowth' => round($profitGrowth, 1),
        ];
    }

    private function getSalesChartData($retailerId, $startDate, $endDate)
    {
        $diffInDays = $startDate->diffInDays($endDate);

        if ($diffInDays > 90) {
            // Group by month if period is longer than 90 days
            $salesData = Order::where('retailer_id', $retailerId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as date'),
                    DB::raw('SUM(total_amount) as total')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $labels = [];
            $values = [];

            foreach ($salesData as $data) {
                $date = Carbon::createFromFormat('Y-m', $data->date);
                $labels[] = $date->format('M Y');
                $values[] = $data->total;
            }
        } elseif ($diffInDays > 30) {
            // Group by week if period is longer than 30 days
            $salesData = Order::where('user_id', $retailerId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(
                    DB::raw('YEARWEEK(created_at, 3) as yearweek'),
                    DB::raw('MIN(created_at) as week_start'),
                    DB::raw('SUM(total_amount) as total')
                )
                ->groupBy('yearweek')
                ->orderBy('yearweek')
                ->get();

            $labels = [];
            $values = [];

            foreach ($salesData as $data) {
                $weekStart = Carbon::parse($data->week_start)->startOfWeek();
                $labels[] = $weekStart->format('M d');
                $values[] = $data->total;
            }
        } else {
            // Group by day
            $salesData = Order::where('retailer_id', $retailerId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(total_amount) as total')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $labels = [];
            $values = [];

            // Fill in any missing dates with zero values
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                $dateStr = $currentDate->format('Y-m-d');
                $data = $salesData->firstWhere('date', $dateStr);

                $labels[] = $currentDate->format('M d');
                $values[] = $data ? $data->total : 0;

                $currentDate->addDay();
            }
        }

        return [
            'labels' => $labels,
            'values' => $values
        ];
    }

    private function getTopProducts($retailerId, $startDate, $endDate)
    {
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.user_id', $retailerId)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'products.id',
                'products.name',
                'products.image',
                'products.category',
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('SUM(order_items.price * order_items.quantity) as revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.image', 'products.category')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        return $topProducts;
    }

    private function getRecentOrders($retailerId)
    {
        return Order::with('user')
            ->where('user_id', $retailerId)
            ->latest()
            ->limit(5)
            ->get();
    }

    private function getCategorySalesData($retailerId, $startDate, $endDate)
    {
        $categoryData = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.user_id', $retailerId)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereNotNull('products.category')
            ->select(
                'products.category',
                DB::raw('SUM(order_items.price * order_items.quantity) as revenue')
            )
            ->groupBy('products.category')
            ->orderByDesc('revenue')
            ->get();

        $labels = $categoryData->pluck('category')->toArray();
        $values = $categoryData->pluck('revenue')->toArray();

        return [
            'labels' => $labels,
            'values' => $values
        ];
    }
}