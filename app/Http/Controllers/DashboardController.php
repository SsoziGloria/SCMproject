<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Worker;
use App\Models\Workforce;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $hour = now()->format('H');
        if ($hour < 12) {
            $greeting = 'HEY!';
        } elseif ($hour < 18) {
            $greeting = 'HEY!';
        } else {
            $greeting = 'HEY!';
        }

        $totalWorkers = Worker::count();
        $totalAssignments = Workforce::count();
        $assignedWorkers = Worker::has('assignments')->count();
        $unassignedWorkers = Worker::doesntHave('assignments')->count();
        $recentAssignments = Workforce::with('worker')->latest()->take(5)->get();

        return view('dashboard', compact(
            'user', 'greeting', 'totalWorkers', 'totalAssignments',
            'assignedWorkers', 'unassignedWorkers', 'recentAssignments'
        ));
    }
} 