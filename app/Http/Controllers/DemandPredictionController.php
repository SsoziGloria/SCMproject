<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DemandPrediction;

class DemandPredictionController extends Controller
{
    public function index()
    {
        $predictions = DemandPrediction::all();
        return response()->json($predictions);
    }
}
