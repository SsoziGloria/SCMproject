<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerSegment;
use App\Models\DemandPrediction;
use App\Models\CustomerClusterSummary;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {

        $segments = DB::table('customer_segments')->paginate(10);
        $predictions = DemandPrediction::all();
        $clusterSummaries = CustomerClusterSummary::all();

        $segmentsAll = DB::table('customer_segments')->get();

        return view('dashboard.admin', compact('segments', 'segmentsAll', 'predictions', 'clusterSummaries'));
    }

}


