<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Workforce;
use App\Models\Worker;

class WorkforceController extends Controller
{
    //show all assignments
    public function index()
    {
        $assignments = Workforce::all();
        return view('workforce.index', compact('assignments'));
    }

    //show form to assign a worker
    public function create()
    {
        $workers = Worker::all();
        return view('workforce.create', compact('workers'));
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
//method fetches all workers and passes them into the form so that u select a worker to assign
        Workforce::create([
            'worker_id' => $request->worker_id,
            'location' => $request->location,
            'task' => $request->task,
            'assigned_date' => $request->assigned_date,
           
        ]);

        return redirect()->route('workforce.index')->with('success', 'Worker assigned successfully!');
    }
    //Dynamic task optimisation
    public function optimize()
    {
        $workers = Worker::all();
        $tasks = Workforce::all();

        // Example logic for task optimization
        foreach ($tasks as $task) {
            $worker = $workers->random(); // Randomly assign a worker for simplicity
            $task->worker_id = $worker->id;
            $task->save();
        }

        return redirect()->route('workforce.index')->with('success', 'Tasks optimized successfully.');
    }
}