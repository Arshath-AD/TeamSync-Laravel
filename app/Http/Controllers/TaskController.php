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
    public function index()
    {
        $tasks = $this->taskService->getAllTasks();
        return view('tasks.index', compact('tasks'));
    }

    /**
     * Display the global Kanban board.
     */
    public function board()
    {
        $board = $this->taskService->getBoardView();
        return view('tasks.board', compact('board'));
    }

    /**
     * Display the authenticated user's tasks dashboard.
     */
    public function myTasks()
    {
        $data = $this->taskService->getUserTasks(Auth::user());
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

        return redirect()->route('tasks.board')
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

        return redirect()->route('tasks.board')
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Update only the status of the specified resource.
     */
    public function updateStatus(UpdateTaskStatusRequest $request, Task $task)
    {
        $this->taskService->updateStatus($task, $request->validated('status'));

        return back()->with('success', 'Task status updated.');
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
