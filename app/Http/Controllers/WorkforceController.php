<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Workforce;
use App\Models\Worker;
use Carbon\Carbon;

class WorkforceController extends Controller
{
   public function index()
{
    // Get all worker IDs that have already been assigned today
    $assignedIds = Workforce::whereDate('assigned_date', today())->pluck('worker_id')->toArray();

    // Get workers who are not yet assigned today
    $unassignedWorkers = Worker::whereNotIn('id', $assignedIds)->get();

    // If new unassigned workers exist, assign them now
    if ($unassignedWorkers->isNotEmpty()) {
        $this->autoAssignRemaining($unassignedWorkers);
    }

    // Fetch today's full assignment list for display
    $assignments = Workforce::with('worker')
        ->whereDate('assigned_date', today())
        ->get();

    return view('workforce.index', compact('assignments'));
}


    //store a new assignment
    public function store(Request $request)
    {
        $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'location' => 'required|string|max:255',
            'task' => 'required|string|max:255',
            'assigned_date' => 'required|date',
            
        ]);
// fetch all workers and passes them into the form
        Workforce::create([
            'worker_id' => $request->worker_id,
            'location' => $request->location,
            'task' => $request->task,
            'assigned_date' => $request->assigned_date,
           
        ]);

        return redirect()->route('workforce.index')->with('success', 'Worker assigned successfully!');
    }
    
    public function optimize()
    {
        $unassignedWorkers = Worker::whereDoesntHave('workforce')->get();

        foreach ($unassignedWorkers as $worker) {
            Workforce::create([
                'worker_id' => $worker->id,
                'location' => 'Default Location',  
                'task' => 'General Support',
                'assigned_date' => Carbon::today(),
            ]);
    }
    return redirect()->route('workforce.index')->with('success', 'Unassigned workers have been automatically assigned.');
} 

    private function autoAssign()
{
    // Get all workers who have NOT been assigned today
    $assignedIds = Workforce::whereDate('assigned_date', today())->pluck('worker_id')->toArray();

    $availableWorkers = Worker::whereNotIn('id', $assignedIds)->get();

    // Define how many people are needed per task
    $taskRequirements = [
        'cocoa_processing' => 3,
        'packaging' => 4,
    ];

    foreach ($taskRequirements as $task => $count) {
        $workersToAssign = $availableWorkers->splice(0, $count);

        foreach ($workersToAssign as $worker) {
            Workforce::create([
                'worker_id' => $worker->id,
                'location' => 'Factory A', // you can make this dynamic later
                'task' => $task,
                'assigned_date' => today(),
            ]);
        }
    }
}

// View old task assignments (e.g. from previous days)

public function history()
{
    $history = Workforce::with('worker')->orderBy('assigned_date', 'desc')->get();
    return view('workforce.history', compact('history'));
}

// View workers who have never been assigned
public function unassigned()
{
    $assignedWorkerIds = Workforce::pluck('worker_id')->unique();
    $unassignedWorkers = Worker::whereNotIn('id', $assignedWorkerIds)->get();

    return view('workforce.unassigned', compact('unassignedWorkers'));
}
private function autoAssignRemaining($availableWorkers)
{
    // Define how many people are needed per task
    $taskRequirements = [
        'cocoa_processing' => 3,
        'packaging' => 4,
    ];

    // Count already assigned per task
    $assignedToday = Workforce::whereDate('assigned_date', today())
                        ->select('task')
                        ->selectRaw('count(*) as count')
                        ->groupBy('task')
                        ->pluck('count', 'task');

    foreach ($taskRequirements as $task => $requiredCount) {
        $alreadyAssigned = $assignedToday[$task] ?? 0;
        $remainingNeeded = $requiredCount - $alreadyAssigned;

        if ($remainingNeeded > 0) {
            $workersToAssign = $availableWorkers->splice(0, $remainingNeeded);

            foreach ($workersToAssign as $worker) {
                Workforce::create([
                    'worker_id' => $worker->id,
                    'location' => 'Factory A',
                    'task' => $task,
                    'assigned_date' => today(),
                ]);
            }
        }
    }
}



}
