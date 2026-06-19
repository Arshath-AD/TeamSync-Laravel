<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Services\TaskService;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tasks    = $this->taskService->getAllTasks($request);
        $projects = \App\Models\Project::orderBy('project_name')->get();
        $users    = \App\Models\User::orderBy('name')->get();
        return view('tasks.index', compact('tasks', 'projects', 'users'));
    }

    /**
     * Display the global Kanban board.
     */
    public function board()
    {
        return redirect()->route('tasks.index');
    }

    /**
     * Display the authenticated user's tasks dashboard.
     */
    public function myTasks(Request $request)
    {
        $data = $this->taskService->getUserTasks(Auth::user(), $request);
        return view('tasks.my_tasks', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::orderBy('project_name')->get();
        $users = User::orderBy('name')->get();
        return view('tasks.create', compact('projects', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $this->taskService->createTask($request->validated());

        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $projects = Project::orderBy('project_name')->get();
        $users = User::orderBy('name')->get();
        return view('tasks.edit', compact('task', 'projects', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->taskService->updateTask($task, $request->validated());

        return redirect()->route('tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Update only the status of the specified resource.
     */
    public function updateStatus(UpdateTaskStatusRequest $request, Task $task)
    {
        $this->taskService->updateStatus($task, $request->validated('status'));
        return back()->with('success', 'Status updated.');
    }

    /**
     * Quick priority change: increase or decrease by one level.
     */
    public function updatePriority(Request $request, Task $task)
    {
        $direction = $request->input('direction'); // 'up' | 'down'

        if ($direction === 'up') {
            $result = $this->taskService->increasePriority($task);
        } elseif ($direction === 'down') {
            $result = $this->taskService->decreasePriority($task);
        } else {
            return back()->with('error', 'Invalid priority direction.');
        }

        if ($result === false) {
            return back()->with('error', 'Priority is already at its limit.');
        }

        return back()->with('success', "Priority changed to {$task->fresh()->priority}.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->taskService->deleteTask($task);
        return back()->with('success', 'Task deleted successfully.');
    }
}
