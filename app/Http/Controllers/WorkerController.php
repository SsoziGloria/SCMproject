<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Worker;

class WorkerController extends Controller
{
    // Show all workers
    public function index()
    {
        $workers = Worker::all();
        return view('workers.index', compact('workers'));
    }

    // Show form to create a new worker
    public function create()
    {
        return view('workers.create');
    }

    // Store a new worker
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:workers,email',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
        ]);

        Worker::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position' => $request->position,
        ]);

        return redirect()->route('workers.index')->with('success', 'Worker added successfully!');
    }

    // Show form to edit a worker
    public function edit(Worker $worker)
    {
        return view('workers.edit', compact('worker'));
    }

    // Update a worker
    public function update(Request $request, Worker $worker)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:workers,email,' . $worker->id,
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
        ]);

        $worker->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position' => $request->position,
        ]);

        return redirect()->route('workers.index')->with('success', 'Worker updated successfully!');
    }

    // Delete a worker
    public function destroy(Worker $worker)
    {
        $worker->delete();
        return redirect()->route('workers.index')->with('success', 'Worker deleted successfully!');
    }
} 