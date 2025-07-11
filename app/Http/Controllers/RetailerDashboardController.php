<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerSegment;
use App\Models\DemandPrediction;

class RetailerDashboardController extends Controller
{
    public function index()
    {
        // You can later add filtering by retailer ID if needed
        $segments = CustomerSegment::all();
        $predictions = DemandPrediction::all();

        return view('dashboard.retailer', compact('segments', 'predictions'));
    }
}
