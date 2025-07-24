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
use Barryvdh\DomPDF\Facade\Pdf;

class RetailerReportController extends Controller
{
    public function index()
    {
        return view('retailer.reports.index');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:sales,inventory,customer-summary,comprehensive',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $reportType = $request->report_type;
        $dateFrom = Carbon::parse($request->date_from)->startOfDay();
        $dateTo = Carbon::parse($request->date_to)->endOfDay();
        $retailerId = Auth::id();

        $reportData = $this->generateReportData($reportType, $dateFrom, $dateTo, $retailerId);

        $pdf = PDF::loadView('retailer.reports.pdf', $reportData);

        $filename = "retailer_{$reportType}_report_" . $dateFrom->format('Y-m-d') . "_to_" . $dateTo->format('Y-m-d') . ".pdf";

        return $pdf->stream($filename);
    }

    public function download($report)
    {
        // Implementation for downloading saved reports
        return redirect()->back()->with('error', 'Report not found.');
    }

    private function generateReportData($reportType, $dateFrom, $dateTo, $retailerId)
    {
        $data = [
            'report_type' => $reportType,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'retailer_id' => $retailerId,
            'generated_at' => now(),
        ];

        switch ($reportType) {
            case 'sales':
                return array_merge($data, $this->generateSalesReportData($dateFrom, $dateTo, $retailerId));
            case 'inventory':
                return array_merge($data, $this->generateInventoryReportData($dateFrom, $dateTo, $retailerId));
            case 'customer-summary':
                return array_merge($data, $this->generateCustomerSummaryReportData($dateFrom, $dateTo, $retailerId));
            case 'comprehensive':
                return array_merge($data, $this->generateComprehensiveReportData($dateFrom, $dateTo, $retailerId));
            default:
                throw new \InvalidArgumentException("Invalid report type: {$reportType}");
        }
    }

    private function generateSalesReportData($dateFrom, $dateTo, $retailerId)
    {
        // Get orders for this retailer (assuming retailer_id or user_id filtering)
        $orders = Order::whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['items.product', 'user'])
            ->get();

        $totalRevenue = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Top products by revenue
        $topProducts = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
            ->select(
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();

        // Daily sales data for chart
        $dailySales = Order::whereBetween('created_at', [$dateFrom, $dateTo])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'average_order_value' => $averageOrderValue,
            'top_products' => $topProducts,
            'daily_sales' => $dailySales,
            'orders' => $orders->take(20), // Recent orders sample
        ];
    }

    private function generateInventoryReportData($dateFrom, $dateTo, $retailerId)
    {
        $inventories = Inventory::with('product')
            ->where('updated_at', '>=', $dateFrom)
            ->get();

        $lowStockItems = $inventories->filter(function ($inventory) {
            return $inventory->quantity <= $inventory->reorder_level;
        });

        $outOfStockItems = $inventories->where('quantity', 0);

        $totalProducts = $inventories->count();
        $totalStockValue = $inventories->sum(function ($inventory) {
            return $inventory->quantity * $inventory->product->price;
        });

        // Inventory movements (based on order items as proxy)
        $movements = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
            ->select(
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                'orders.created_at'
            )
            ->groupBy('products.id', 'products.name', 'orders.created_at')
            ->orderBy('orders.created_at', 'desc')
            ->get();

        return [
            'total_products' => $totalProducts,
            'total_stock_value' => $totalStockValue,
            'low_stock_count' => $lowStockItems->count(),
            'out_of_stock_count' => $outOfStockItems->count(),
            'low_stock_items' => $lowStockItems,
            'out_of_stock_items' => $outOfStockItems,
            'inventory_movements' => $movements->take(50),
            'all_inventories' => $inventories,
        ];
    }

    private function generateCustomerSummaryReportData($dateFrom, $dateTo, $retailerId)
    {
        // Customer analysis based on orders
        $customers = User::whereHas('orders', function ($query) use ($dateFrom, $dateTo) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        })->with(['orders' => function ($query) use ($dateFrom, $dateTo) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        }])->get();

        $totalCustomers = $customers->count();
        $newCustomers = User::whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereHas('orders')
            ->count();

        // Customer spending analysis
        $customerSpending = $customers->map(function ($customer) {
            $totalSpent = $customer->orders->sum('total_amount');
            $orderCount = $customer->orders->count();
            return [
                'name' => $customer->name,
                'email' => $customer->email,
                'total_spent' => $totalSpent,
                'order_count' => $orderCount,
                'average_order_value' => $orderCount > 0 ? $totalSpent / $orderCount : 0,
            ];
        })->sortByDesc('total_spent');

        $topCustomers = $customerSpending->take(10);

        return [
            'total_customers' => $totalCustomers,
            'new_customers' => $newCustomers,
            'top_customers' => $topCustomers,
            'customer_spending' => $customerSpending->take(20),
        ];
    }

    private function generateComprehensiveReportData($dateFrom, $dateTo, $retailerId)
    {
        // Combine all report types
        $salesData = $this->generateSalesReportData($dateFrom, $dateTo, $retailerId);
        $inventoryData = $this->generateInventoryReportData($dateFrom, $dateTo, $retailerId);
        $customerData = $this->generateCustomerSummaryReportData($dateFrom, $dateTo, $retailerId);

        return array_merge($salesData, $inventoryData, $customerData, [
            'report_sections' => ['sales', 'inventory', 'customer-summary']
        ]);
    }
}
