<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerSegment;
use App\Models\DemandPrediction;
use App\Models\CustomerClusterSummary;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $pendingOrders = Order::where('status', 'pending')->count();
        $returns = Order::where('status', 'cancelled')->count();
        $deliveredOrders = Order::where('status', 'delivered')->count();
        $totalRevenue = Order::where('status', 'delivered')->sum('total_amount');

        $segments = CustomerSegment::orderBy('created_at', 'desc')->take(50)->get();

        $predictions = DemandPrediction::orderBy('prediction_date', 'desc')->take(20)->get();

        $clusterSummaries = CustomerClusterSummary::all();

        return view('dashboard.admin', compact(
            'segments',
            'predictions',
            'clusterSummaries',
            'pendingOrders',
            'returns',
            'deliveredOrders',
            'totalRevenue'
        ));
    }
}
