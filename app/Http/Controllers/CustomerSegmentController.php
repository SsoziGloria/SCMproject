<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CustomerSegment;

class CustomerSegmentController extends Controller
{
     public function index()
    {
       $segments = CustomerSegment::all();
    return response()->json($segments);
    }
}
