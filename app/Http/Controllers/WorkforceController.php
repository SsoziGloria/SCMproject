<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Workforces;
use App\Models\Worker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\Task;

class WorkforceController extends Controller
{
    public function index()
    {
        $availableWorkers = Worker::where('status', 'available')->get();

        $availableWorkers = $availableWorkers->shuffle();

        if ($availableWorkers->isNotEmpty()) {
            $this->runAutoAssignment($availableWorkers);
        }

        $assignments = Workforces::with('worker')
            ->whereDate('assigned_date', today())
            ->orderBy('status', 'asc') // 'assigned' comes before 'completed'
            ->orderBy('created_at', 'desc')
            ->get();

        return view('workforce.index', compact('assignments'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'location' => 'required|string|max:255',
            'task' => 'required|string|max:255',
            'assigned_date' => 'required|date',

        ]);
        Workforces::create([
            'worker_id' => $request->worker_id,
            'location' => $request->location,
            'task' => $request->task,
            'assigned_date' => $request->assigned_date,

        ]);

        return redirect()->route('workforce.index')->with('success', 'Worker assigned successfully!');
    }

    public function finishTask(Workforces $assignment)
    {
        $assignment->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $worker = $assignment->worker;
        if ($worker) {
            $worker->update(['status' => 'available']);

            $this->runAutoAssignment(collect([$worker]));
            $message = "Task for {$worker->name} completed. Worker is now available for new tasks.";
        } else {
            $message = "Task completed, but the associated worker could not be found.";
        }

        return redirect()->route('workforce.index')->with('success', $message);
    }

    private function autoAssign()
    {
        $assignedIds = Workforces::whereDate('assigned_date', today())->pluck('worker_id')->toArray();

        $availableWorkers = Worker::whereNotIn('id', $assignedIds)->get();
        $taskRequirements = [
            'cocoa_processing' => 3,
            'packaging' => 4,
        ];

        foreach ($taskRequirements as $task => $count) {
            $workersToAssign = $availableWorkers->splice(0, $count);

            foreach ($workersToAssign as $worker) {
                Workforces::create([
                    'worker_id' => $worker->id,
                    'location' => 'Factory A',
                    'task' => $task,
                    'assigned_date' => today(),
                ]);
            }
        }
    }

    public function runAutoAssignment(Collection $availableWorkers): int
    {
        $activeTasks = Task::where('is_active', true)
            ->where('status_for_day', 'pending')
            ->orderBy('priority', 'desc')
            ->get();

        $assignmentsMade = 0;

        foreach ($activeTasks as $task) {
            if ($availableWorkers->isEmpty()) {
                break;
            }

            $assignedTodayCount = Workforces::whereDate('assigned_date', today())
                ->where('task', $task->name)
                ->count();

            $needed = $task->required_workers - $assignedTodayCount;

            if ($needed > 0) {
                $workersAlreadyOnTaskToday = Workforces::whereDate('assigned_date', today())
                    ->where('task', $task->name)
                    ->pluck('worker_id')
                    ->all();

                $eligibleWorkers = $availableWorkers->whereNotIn('id', $workersAlreadyOnTaskToday);

                $workersToAssign = $eligibleWorkers->take($needed);

                if ($workersToAssign->isEmpty()) {
                    continue;
                }

                foreach ($workersToAssign as $worker) {
                    Workforces::create([
                        'worker_id' => $worker->id,
                        'location' => $task->location,
                        'task' => $task->name,
                        'assigned_date' => today(),
                        'status' => 'assigned',
                    ]);
                    $worker->update(['status' => 'assigned']);
                    $assignmentsMade++;
                }

                $assignedWorkerIds = $workersToAssign->pluck('id');
                $availableWorkers = $availableWorkers->whereNotIn('id', $assignedWorkerIds);
            }
            $totalAssignedNow = $assignedTodayCount + $assignmentsMade;
            if ($totalAssignedNow >= $task->required_workers) {
                $task->update(['status_for_day' => 'staffed']);
            }
        }
        return $assignmentsMade;
    }

    public function history()
    {
        $history = Workforces::with('worker')
            ->latest('assigned_date')
            ->latest('completed_at')
            ->get()
            ->groupBy(function ($assignment) {
                return \Carbon\Carbon::parse($assignment->assigned_date)->toFormattedDateString();
            });

        return view('workforce.history', compact('history'));
    }

    public function unassigned()
    {
        $unassignedWorkers = Worker::where('status', 'available')->get();
        return view('workforce.unassigned', compact('unassignedWorkers'));
    }

    public function create()
    {
        $availableWorkers = Worker::where('status', 'available')->orderBy('name')->get();
        $activeTasks = Task::where('is_active', true)->orderBy('name')->get();

        return view('workforce.create', compact('availableWorkers', 'activeTasks'));
    }
}
