<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class TaskService
{
    /**
     * Get all tasks with relationships eager loaded.
     */
    public function getAllTasks(): Collection
    {
        return Task::with(['project', 'assignee'])
            ->orderByRaw('FIELD(priority, "Critical", "High", "Medium", "Low")')
            ->orderBy('deadline', 'asc')
            ->get();
    }

    /**
     * Get tasks grouped by status for a Kanban board.
     * Maps 'Pending' to 'Todo'.
     */
    public function getBoardView($tasks = null): array
    {
        $tasks = $tasks ?? $this->getAllTasks();

        return [
            'Todo' => $tasks->where('status', 'Pending')->values(),
            'In Progress' => $tasks->where('status', 'In Progress')->values(),
            'Completed' => $tasks->where('status', 'Completed')->values(),
        ];
    }

    /**
     * Get tasks assigned to a specific user.
     * Identifies overdue and upcoming deadlines.
     */
    public function getUserTasks(User $user): array
    {
        $tasks = Task::with(['project'])
            ->where('assigned_to', $user->id)
            ->orderByRaw('FIELD(priority, "Critical", "High", "Medium", "Low")')
            ->orderBy('deadline', 'asc')
            ->get();

        $today = now()->startOfDay();
        
        $overdue = $tasks->filter(function ($task) use ($today) {
            return $task->status !== 'Completed' && $task->deadline && \Carbon\Carbon::parse($task->deadline)->isBefore($today);
        })->values();

        $upcoming = $tasks->filter(function ($task) use ($today) {
            return $task->status !== 'Completed' && (!$task->deadline || \Carbon\Carbon::parse($task->deadline)->greaterThanOrEqualTo($today));
        })->values();

        return [
            'tasks' => $tasks,
            'overdue' => $overdue,
            'upcoming' => $upcoming,
            'board' => $this->getBoardView($tasks),
        ];
    }

    /**
     * Create a new task.
     */
    public function createTask(array $data): Task
    {
        return Task::create($data);
    }

    /**
     * Update an existing task.
     */
    public function updateTask(Task $task, array $data): Task
    {
        $task->update($data);
        return $task;
    }

    /**
     * Update only the status of a task.
     */
    public function updateStatus(Task $task, string $status): Task
    {
        $task->update(['status' => $status]);
        return $task;
    }

    /**
     * Delete a task.
     */
    public function deleteTask(Task $task): bool
    {
        return $task->delete();
    }
}
