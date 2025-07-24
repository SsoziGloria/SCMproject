<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\CustomerSegment;
use App\Models\DemandPrediction;
use App\Models\Order;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Report;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;


class ReportController extends Controller
{
    /**
     * Show reports dashboard
     */
    public function index()
    {
        $recentReports = Report::orderBy('created_at', 'desc')->take(10)->get();
        $reportTypes = [
            'sales' => 'Sales Report',
            'inventory' => 'Inventory Report',
            'ml-analysis' => 'ML Analysis Report',
            'customer-segments' => 'Customer Segmentation Report',
            'demand-forecast' => 'Demand Forecast Report',
            'comprehensive' => 'Comprehensive Business Report'
        ];

        return view('admin.reports.index', compact('recentReports', 'reportTypes'));
    }

    /**
     * Generate and optionally email a report
     */
    public function generate(Request $request)
    {
        $request->validate([
            'type' => 'required|in:sales,inventory,ml-analysis,customer-segments,demand-forecast,comprehensive',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'format' => 'required|in:pdf,csv,excel,json',
            'email_recipients' => 'nullable|string',
            'schedule_frequency' => 'nullable|in:daily,weekly,monthly'
        ]);

        try {
            $reportData = $this->generateReportData($request->type, $request->date_from, $request->date_to);

            // Create report record
            $report = Report::create([
                'name' => ucfirst($request->type) . ' Report',
                'type' => $request->type,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'format' => $request->format,
                'status' => 'processing',
                'generated_by' => Auth::id(),
                'data' => json_encode($reportData),
                'email_recipients' => $request->email_recipients
            ]);            // Generate file based on format
            $fileName = $this->generateReportFile($report, $reportData, $request->format);

            // Update report with file path
            $report->update([
                'file_path' => $fileName,
                'status' => 'completed'
            ]);

            // Send emails if recipients provided
            if ($request->email_recipients) {
                $this->emailReport($report, $request->email_recipients);
            }

            // Schedule recurring reports if requested
            if ($request->schedule_frequency) {
                $this->scheduleRecurringReport($request->all());
            }

            return response()->json([
                'success' => true,
                'message' => 'Report generated successfully!',
                'report_id' => $report->id,
                'download_url' => route('admin.reports.download', $report->id)
            ]);
        } catch (\Exception $e) {
            Log::error('Report generation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Report generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate report data based on type
     */
    private function generateReportData($type, $dateFrom, $dateTo)
    {
        $from = Carbon::parse($dateFrom);
        $to = Carbon::parse($dateTo);

        switch ($type) {
            case 'sales':
                return $this->generateSalesReportData($from, $to);
            case 'inventory':
                return $this->generateInventoryReportData($from, $to);
            case 'ml-analysis':
                return $this->generateMLAnalysisReportData($from, $to);
            case 'customer-segments':
                return $this->generateCustomerSegmentsReportData($from, $to);
            case 'demand-forecast':
                return $this->generateDemandForecastReportData($from, $to);
            case 'comprehensive':
                return $this->generateComprehensiveReportData($from, $to);
            default:
                throw new \Exception('Invalid report type');
        }
    }

    private function generateSalesReportData($from, $to)
    {
        $orders = Order::whereBetween('created_at', [$from, $to])->get();

        return [
            'period' => "{$from->format('Y-m-d')} to {$to->format('Y-m-d')}",
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->where('status', 'delivered')->sum('total_amount'),
            'pending_orders' => $orders->where('status', 'pending')->count(),
            'cancelled_orders' => $orders->where('status', 'cancelled')->count(),
            'average_order_value' => $orders->where('status', 'delivered')->avg('total_amount'),
            'daily_breakdown' => $orders->groupBy(function ($order) {
                return $order->created_at->format('Y-m-d');
            })->map(function ($dayOrders) {
                return [
                    'orders' => $dayOrders->count(),
                    'revenue' => $dayOrders->where('status', 'delivered')->sum('total_amount')
                ];
            })
        ];
    }

    private function generateInventoryReportData($from, $to)
    {
        $inventories = Inventory::with('product')->get();

        return [
            'period' => "{$from->format('Y-m-d')} to {$to->format('Y-m-d')}",
            'total_products' => $inventories->count(),
            'low_stock_products' => $inventories->where('quantity', '<', 10)->count(),
            'out_of_stock_products' => $inventories->where('quantity', 0)->count(),
            'total_inventory_value' => $inventories->sum(function ($inv) {
                return $inv->quantity * ($inv->product->price ?? 0);
            }),
            'products_detail' => $inventories->map(function ($inv) {
                return [
                    'product_name' => $inv->product->name ?? 'Unknown',
                    'current_stock' => $inv->quantity,
                    'status' => $inv->quantity == 0 ? 'Out of Stock' : ($inv->quantity < 10 ? 'Low Stock' : 'In Stock'),
                    'value' => $inv->quantity * ($inv->product->price ?? 0)
                ];
            })
        ];
    }

    private function generateMLAnalysisReportData($from, $to)
    {
        // For customer segments, check if updated_at exists, otherwise use created_at only
        $segmentsQuery = CustomerSegment::query();
        try {
            $segments = $segmentsQuery->whereBetween('created_at', [$from, $to])->get();
        } catch (\Exception $e) {
            // If created_at filtering fails, get all segments
            $segments = CustomerSegment::all();
        }

        // For demand predictions, filter by prediction_date
        try {
            $predictions = DemandPrediction::whereBetween('prediction_date', [$from, $to])->get();
        } catch (\Exception $e) {
            // If date filtering fails, get all predictions
            $predictions = DemandPrediction::all();
        }

        return [
            'period' => "{$from->format('Y-m-d')} to {$to->format('Y-m-d')}",
            'customer_segments' => [
                'total_segments' => $segments->count(),
                'cluster_breakdown' => $segments->groupBy('cluster')->map(function ($cluster) {
                    return [
                        'count' => $cluster->count(),
                        'avg_quantity' => $cluster->avg('total_quantity'),
                        'total_quantity' => $cluster->sum('total_quantity')
                    ];
                })
            ],
            'demand_predictions' => [
                'total_predictions' => $predictions->count(),
                'total_predicted_demand' => $predictions->sum('predicted_quantity'),
                'avg_predicted_demand' => $predictions->avg('predicted_quantity'),
                'top_predicted_products' => $predictions->groupBy('product_id')
                    ->map(function ($productPredictions) {
                        return $productPredictions->sum('predicted_quantity');
                    })->sortDesc()->take(10)
            ]
        ];
    }

    private function generateCustomerSegmentsReportData($from, $to)
    {
        $segments = CustomerSegment::whereBetween('created_at', [$from, $to])->get();

        return [
            'period' => "{$from->format('Y-m-d')} to {$to->format('Y-m-d')}",
            'total_customers_segmented' => $segments->count(),
            'clusters' => $segments->groupBy('cluster')->map(function ($cluster, $clusterNum) {
                return [
                    'cluster_number' => $clusterNum,
                    'customer_count' => $cluster->count(),
                    'avg_quantity' => $cluster->avg('total_quantity'),
                    'total_quantity' => $cluster->sum('total_quantity'),
                    'recommended_strategy' => $this->getMarketingStrategy($clusterNum),
                    'customers' => $cluster->take(10)->map(function ($segment) {
                        return [
                            'customer_id' => $segment->customer_id,
                            'total_quantity' => $segment->total_quantity,
                            'total_amount' => 0 // Placeholder since we don't track this in segments
                        ];
                    })
                ];
            })
        ];
    }

    private function generateDemandForecastReportData($from, $to)
    {
        $predictions = DemandPrediction::with('product')->whereBetween('prediction_date', [$from, $to])->get();

        return [
            'period' => "{$from->format('Y-m-d')} to {$to->format('Y-m-d')}",
            'total_predictions' => $predictions->count(),
            'forecast_accuracy' => 'N/A', // Would need historical comparison
            'product_forecasts' => $predictions->groupBy('product_id')->map(function ($productPredictions, $productId) {
                $totalPredicted = $productPredictions->sum('predicted_quantity');
                $avgPredicted = $productPredictions->avg('predicted_quantity');

                return [
                    'product_id' => $productId,
                    'product_name' => $productPredictions->first()->product->name ?? 'Unknown',
                    'total_predicted_demand' => $totalPredicted,
                    'average_predicted_demand' => $avgPredicted,
                    'recommended_stock_level' => ceil($avgPredicted * 1.2),
                    'prediction_count' => $productPredictions->count()
                ];
            })->sortByDesc('total_predicted_demand')
        ];
    }

    private function generateComprehensiveReportData($from, $to)
    {
        return [
            'sales' => $this->generateSalesReportData($from, $to),
            'inventory' => $this->generateInventoryReportData($from, $to),
            'ml_analysis' => $this->generateMLAnalysisReportData($from, $to),
            'executive_summary' => [
                'total_revenue' => Order::whereBetween('created_at', [$from, $to])->where('status', 'delivered')->sum('total_amount'),
                'total_orders' => Order::whereBetween('created_at', [$from, $to])->count(),
                'inventory_value' => Inventory::with('product')->get()->sum(function ($inv) {
                    return $inv->quantity * ($inv->product->price ?? 0);
                }),
                'ml_insights_available' => CustomerSegment::count() > 0 && DemandPrediction::count() > 0
            ]
        ];
    }

    /**
     * Generate report file based on format
     */
    private function generateReportFile($report, $data, $format)
    {
        $fileName = "report_{$report->id}_{$report->type}_" . now()->format('Y-m-d_H-i-s');

        switch ($format) {
            case 'pdf':
                return $this->generatePDF($report, $data, $fileName);
            case 'csv':
                return $this->generateCSV($report, $data, $fileName);
            case 'excel':
                return $this->generateExcel($report, $data, $fileName);
            case 'json':
                return $this->generateJSON($report, $data, $fileName);
        }
    }

    private function generatePDF($report, $data, $fileName)
    {
        $pdf = PDF::loadView('admin.reports.pdf', compact('report', 'data'));
        $fileName = $fileName . '.pdf';
        $filePath = 'reports/' . $fileName;

        // Save PDF content to storage
        Storage::put($filePath, $pdf->output());

        return $filePath;
    }

    private function generateCSV($report, $data, $fileName)
    {
        $fileName = $fileName . '.csv';
        $filePath = 'reports/' . $fileName;

        // Create CSV content
        $csvContent = '';

        // CSV header
        $csvContent .= "ChocolateSCM Business Report\n";
        $csvContent .= "Report: {$report->name}\n";
        $csvContent .= "Period: {$report->date_from} to {$report->date_to}\n";
        $csvContent .= "Generated: " . now()->format('Y-m-d H:i:s') . "\n\n";

        // Write data based on report type
        if ($report->type === 'sales') {
            $csvContent .= "SALES SUMMARY\n";
            $csvContent .= "Metric,Value\n";
            $csvContent .= "Total Revenue,UGX " . number_format($data['total_revenue'] ?? 0, 0) . "\n";
            $csvContent .= "Total Orders," . number_format($data['total_orders'] ?? 0) . "\n";
            $csvContent .= "Pending Orders," . number_format($data['pending_orders'] ?? 0) . "\n";
            $csvContent .= "Average Order Value,UGX " . number_format($data['average_order_value'] ?? 0, 0) . "\n";
        } elseif ($report->type === 'inventory') {
            $csvContent .= "INVENTORY SUMMARY\n";
            $csvContent .= "Product Name,Current Stock,Status,Value\n";
            foreach ($data['products_detail'] ?? [] as $product) {
                $csvContent .= "\"{$product['product_name']}\",{$product['current_stock']},{$product['status']},UGX " . number_format($product['value'], 0) . "\n";
            }
        }

        // Save to storage
        Storage::put($filePath, $csvContent);

        return $filePath;
    }

    private function generateExcel($report, $data, $fileName)
    {
        $fileName = $fileName . '.xlsx';
        $path = 'reports/' . $fileName;

        // Create Excel export using Laravel Excel
        Excel::store(new \App\Exports\ReportExport($report, $data), $path);

        return $path;
    }

    private function generateJSON($report, $data, $fileName)
    {
        $fileName = $fileName . '.json';
        $filePath = 'reports/' . $fileName;

        // Save JSON to storage
        Storage::put($filePath, json_encode($data, JSON_PRETTY_PRINT));

        return $filePath;
    }

    /**
     * Email report to recipients
     */
    private function emailReport($report, $recipients)
    {
        $recipientEmails = array_map('trim', explode(',', $recipients));

        foreach ($recipientEmails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Mail::send('emails.report', compact('report'), function ($message) use ($email, $report) {
                    $message->to($email)
                        ->subject("Automated Report: {$report->name}")
                        ->attach(Storage::path($report->file_path));
                });
            }
        }
    }

    /**
     * Get marketing strategy for cluster (shared with MLAnalysisController)
     */
    private function getMarketingStrategy($cluster)
    {
        $strategies = [
            0 => 'Premium product focus with personalized offers',
            1 => 'Bulk discounts and wholesale pricing',
            2 => 'Regular promotions and loyalty rewards',
            3 => 'New customer acquisition campaigns',
            4 => 'Retention campaigns with exclusive offers'
        ];

        return $strategies[$cluster] ?? 'General marketing approach';
    }

    /**
     * Download a report file
     */
    public function download(Report $report)
    {
        if (!$report->file_path || !Storage::exists($report->file_path)) {
            abort(404, 'Report file not found');
        }

        return Storage::download($report->file_path, $report->name . '.' . $report->format);
    }

    /**
     * Schedule recurring report
     */
    private function scheduleRecurringReport($reportConfig)
    {
        // This would integrate with Laravel's task scheduler
        // For now, we'll just log the scheduled report
        Log::info('Recurring report scheduled', $reportConfig);

        // In a production environment, you would:
        // 1. Store the schedule in a database table
        // 2. Use Laravel's task scheduler to run the reports
        // 3. Set up proper queue handling for email delivery
    }
}
