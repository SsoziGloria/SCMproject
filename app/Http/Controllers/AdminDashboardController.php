<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerSegment;
use App\Models\DemandPrediction;

class AdminDashboardController extends Controller
{
    public function index()
    {

        $segments = CustomerSegment::all();
        $predictions = DemandPrediction::all();

        return view('dashboard.admin', compact('segments', 'predictions'));
    }
}
