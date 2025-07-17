<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryAdjustment;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryAdjustmentController extends Controller
{
    /**
     * Display a listing of the inventory adjustments.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = InventoryAdjustment::with(['inventory', 'user'])
            ->latest();

        // Filter by product
        if ($request->has('product_id') && $request->product_id) {
            $query->whereHas('inventory', function ($q) use ($request) {
                $q->where('product_id', $request->product_id);
            });
        }

        // Filter by adjustment type
        if ($request->has('adjustment_type') && $request->adjustment_type) {
            $query->where('adjustment_type', $request->adjustment_type);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $adjustments = $query->paginate(20);

        // Get data for filters
        $products = Product::orderBy('name')->get(['id', 'name']);

        // Get adjustment types for filter
        $adjustmentTypes = InventoryAdjustment::select('adjustment_type')
            ->distinct()
            ->pluck('adjustment_type')
            ->toArray();

        // Get summary statistics
        $stats = [
            'total_adjustments' => InventoryAdjustment::count(),
            'increases' => InventoryAdjustment::where('adjustment_type', 'increase')->count(),
            'decreases' => InventoryAdjustment::where('adjustment_type', 'decrease')->count(),
            'corrections' => InventoryAdjustment::where('adjustment_type', 'correction')->count(),
            'damages' => InventoryAdjustment::where('adjustment_type', 'damage')->count(),
            'expiries' => InventoryAdjustment::where('adjustment_type', 'expiry')->count(),
        ];

        return view('inventories.adjustments', compact('adjustments', 'products', 'adjustmentTypes', 'stats'));
    }

    /**
     * Show the form for creating a new inventory adjustment.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $inventories = Inventory::with('product')
            ->orderBy('product_name')
            ->get();

        $adjustmentTypes = [
            'increase' => 'Increase (Add Stock)',
            'decrease' => 'Decrease (Remove Stock)',
            'correction' => 'Correction (Count Error)',
            'damage' => 'Damage Write-off',
            'expiry' => 'Expiry Write-off'
        ];

        return view('inventories.adjustments_create', compact('inventories', 'adjustmentTypes'));
    }

    /**
     * Store a newly created inventory adjustment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'adjustment_type' => 'required|in:increase,decrease,correction,damage,expiry',
            'quantity_change' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $inventory = Inventory::findOrFail($validated['inventory_id']);

            // Calculate new quantity based on adjustment type
            $oldQuantity = $inventory->quantity;
            $newQuantity = $oldQuantity;
            $actualChange = $validated['quantity_change'];

            switch ($validated['adjustment_type']) {
                case 'increase':
                    $newQuantity = $oldQuantity + $validated['quantity_change'];
                    break;

                case 'decrease':
                case 'damage':
                case 'expiry':
                    $newQuantity = $oldQuantity - $validated['quantity_change'];
                    if ($newQuantity < 0) {
                        return back()->withErrors([
                            'quantity_change' => 'Cannot decrease more than the current quantity.'
                        ])->withInput();
                    }
                    // For these types, change should be negative in the record
                    $actualChange = -$validated['quantity_change'];
                    break;

                case 'correction':
                    $newQuantity = $validated['quantity_change'];
                    $actualChange = $newQuantity - $oldQuantity;
                    break;
            }

            // Create adjustment record
            $adjustment = InventoryAdjustment::create([
                'inventory_id' => $validated['inventory_id'],
                'adjustment_type' => $validated['adjustment_type'],
                'quantity_change' => $actualChange,
                'reason' => $validated['reason'],
                'notes' => $validated['notes'],
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
            ]);

            // Update inventory quantity
            $inventory->quantity = $newQuantity;

            // Update status based on adjustment type if needed
            if ($validated['adjustment_type'] === 'damage') {
                $inventory->status = 'damaged';
            } elseif ($validated['adjustment_type'] === 'expiry') {
                $inventory->status = 'expired';
            }

            $inventory->save();

            DB::commit();

            return redirect()->route('inventories.adjustments')
                ->with('success', 'Inventory adjustment recorded successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Inventory adjustment error: ' . $e->getMessage());

            return back()->withErrors([
                'general' => 'An error occurred while processing your request. Please try again.'
            ])->withInput();
        }
    }

    /**
     * Display the specified adjustment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $adjustment = InventoryAdjustment::with(['inventory', 'user'])
            ->findOrFail($id);

        return view('inventories.adjustments_show', compact('adjustment'));
    }

    /**
     * Generate a report of inventory adjustments.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->subDays(30)->format('Y-m-d');
        $dateTo = $request->date_to ?? now()->format('Y-m-d');

        $adjustments = InventoryAdjustment::with(['inventory', 'user'])
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->get();

        // Group adjustments by date
        $adjustmentsByDate = $adjustments->groupBy(function ($item) {
            return $item->created_at->format('Y-m-d');
        });

        // Group adjustments by type
        $adjustmentsByType = $adjustments->groupBy('adjustment_type');

        // Group adjustments by user
        $adjustmentsByUser = $adjustments->groupBy('user_name');

        // Summary statistics
        $stats = [
            'total_count' => $adjustments->count(),
            'increases' => $adjustments->where('adjustment_type', 'increase')->count(),
            'decreases' => $adjustments->where('adjustment_type', 'decrease')->count(),
            'net_change' => $adjustments->sum('quantity_change'),
            'date_range' => [
                'from' => $dateFrom,
                'to' => $dateTo,
            ],
        ];

        return view('inventories.adjustments_report', compact(
            'adjustments',
            'adjustmentsByDate',
            'adjustmentsByType',
            'adjustmentsByUser',
            'stats',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Export adjustments to CSV or Excel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->subDays(30)->format('Y-m-d');
        $dateTo = $request->date_to ?? now()->format('Y-m-d');

        $adjustments = InventoryAdjustment::with(['inventory', 'user'])
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->get();

        // Generate filename with date range
        $filename = "inventory_adjustments_{$dateFrom}_to_{$dateTo}.csv";

        // Generate CSV content
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($adjustments) {
            $file = fopen('php://output', 'w');

            // Add CSV header
            fputcsv($file, [
                'ID',
                'Date',
                'Product ID',
                'Product Name',
                'Adjustment Type',
                'Quantity Change',
                'Reason',
                'Notes',
                'User',
            ]);

            // Add rows
            foreach ($adjustments as $adjustment) {
                fputcsv($file, [
                    $adjustment->id,
                    $adjustment->created_at->format('Y-m-d H:i:s'),
                    $adjustment->inventory->product_id ?? 'N/A',
                    $adjustment->inventory->product_name ?? 'Unknown Product',
                    $adjustment->adjustment_type,
                    $adjustment->quantity_change,
                    $adjustment->reason,
                    $adjustment->notes,
                    $adjustment->user_name ?? 'System',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display adjustment analytics and charts.
     *
     * @return \Illuminate\Http\Response
     */
    public function analytics()
    {
        // Get adjustments for the last 30 days
        $startDate = now()->subDays(30);
        $endDate = now();

        $adjustments = InventoryAdjustment::with(['inventory'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->get();

        // Group by date for trend chart
        $adjustmentsByDate = $adjustments->groupBy(function ($item) {
            return $item->created_at->format('Y-m-d');
        })->map(function ($items) {
            return [
                'increases' => $items->where('adjustment_type', 'increase')->sum('quantity_change'),
                'decreases' => abs($items->where('adjustment_type', 'decrease')->sum('quantity_change')),
                'damages' => abs($items->where('adjustment_type', 'damage')->sum('quantity_change')),
                'expiries' => abs($items->where('adjustment_type', 'expiry')->sum('quantity_change')),
                'corrections' => $items->where('adjustment_type', 'correction')->count(),
                'total' => $items->count(),
            ];
        });

        // Get adjustment type distribution for pie chart
        $typeDistribution = [
            'increases' => $adjustments->where('adjustment_type', 'increase')->count(),
            'decreases' => $adjustments->where('adjustment_type', 'decrease')->count(),
            'damages' => $adjustments->where('adjustment_type', 'damage')->count(),
            'expiries' => $adjustments->where('adjustment_type', 'expiry')->count(),
            'corrections' => $adjustments->where('adjustment_type', 'correction')->count(),
        ];

        // Get top products with most adjustments
        $topProducts = $adjustments->groupBy('inventory.product_name')
            ->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'increases' => $items->where('adjustment_type', 'increase')->sum('quantity_change'),
                    'decreases' => abs($items->where('adjustment_type', 'decrease')->sum('quantity_change')),
                    'net_change' => $items->sum('quantity_change'),
                ];
            })
            ->sortByDesc('count')
            ->take(5);

        // Top users making adjustments
        $topUsers = $adjustments->groupBy('user_name')
            ->map(function ($items) {
                return $items->count();
            })
            ->sortDesc()
            ->take(5);

        return view('inventories.adjustments_analytics', compact(
            'adjustmentsByDate',
            'typeDistribution',
            'topProducts',
            'topUsers',
            'startDate',
            'endDate'
        ));
    }
}