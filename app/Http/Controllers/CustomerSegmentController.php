<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CustomerSegmentController extends Controller
{
     public function index()
    {
        // Fetch all customer segments from DB
        $segments = DB::table('customer_segments')->get();

        // Pass data to the view
        return view('admin.customer_segments.index', compact('segments'));
    }
}
