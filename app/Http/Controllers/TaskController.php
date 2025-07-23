<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Worker;
use App\Models\Workforce;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::orderBy('priority', 'desc')->get();
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'required_workers' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'priority' => 'required|integer|min:1|max:10',
            'is_active' => 'boolean',
        ]);

        Task::create($request->all());

        return redirect()->route('tasks.index')->with('success', 'Task created successfully!');
    }

    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'required_workers' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'priority' => 'required|integer|min:1|max:10',
            'is_active' => 'boolean',
        ]);

        $task->update($request->all());

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully!');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully!');
    }

    // public function autoAssign()
    // {
    //     $availableWorkers = Worker::where('status', 'available')->get();

    //     if ($availableWorkers->isEmpty()) {
    //         return redirect()->route('tasks.index')->with('success', 'No workers are currently available for assignment.');
    //     }

    //     $workforceController = new WorkforceController();
    //     $assignedCount = $workforceController->runAutoAssignment($availableWorkers);

    //     $message = $assignedCount > 0
    //         ? "Auto-assignment complete. {$assignedCount} new assignments were made."
    //         : "Auto-assignment ran, but no tasks currently need workers or all positions are filled.";

    //     return redirect()->route('tasks.index')->with('success', $message);
    // }
}
