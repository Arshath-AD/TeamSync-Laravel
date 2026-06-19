<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class TaskService
{
    /**
     * Priority ladder (ascending order of importance).
     */
    protected const PRIORITIES = ['Low', 'Medium', 'High', 'Critical'];

    /**
     * Get all tasks with relationships eager loaded.
     * Accepts an optional Request for future filtering.
     */
    public function getAllTasks(?Request $request = null): Collection
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
            'Todo'        => $tasks->where('status', 'Pending')->values(),
            'In Progress' => $tasks->where('status', 'In Progress')->values(),
            'On Hold'     => $tasks->where('status', 'On Hold')->values(),
            'Completed'   => $tasks->where('status', 'Completed')->values(),
        ];
    }

    /**
     * Get tasks assigned to a specific user.
     * Identifies overdue and upcoming deadlines.
     * On Hold tasks are excluded from overdue count.
     */
    public function getUserTasks(User $user, ?Request $request = null): array
    {
        $tasks = Task::with(['project'])
            ->where('assigned_to', $user->id)
            ->orderByRaw('FIELD(priority, "Critical", "High", "Medium", "Low")')
            ->orderBy('deadline', 'asc')
            ->get();

        $today = now()->startOfDay();

        $overdue = $tasks->filter(function ($task) use ($today) {
            return !in_array($task->status, ['Completed', 'On Hold'])
                && $task->deadline
                && \Carbon\Carbon::parse($task->deadline)->isBefore($today);
        })->values();

        $upcoming = $tasks->filter(function ($task) use ($today) {
            return $task->status !== 'Completed'
                && (!$task->deadline || \Carbon\Carbon::parse($task->deadline)->greaterThanOrEqualTo($today));
        })->values();

        return [
            'tasks'    => $tasks,
            'overdue'  => $overdue,
            'upcoming' => $upcoming,
            'board'    => $this->getBoardView($tasks),
        ];
    }

    /**
     * Create a new task.
     */
    public function createTask(array $data): Task
    {
        // Normalise empty project_id to null
        if (isset($data['project_id']) && $data['project_id'] === '') {
            $data['project_id'] = null;
        }
        return Task::create($data);
    }

    /**
     * Update an existing task.
     */
    public function updateTask(Task $task, array $data): Task
    {
        // Normalise empty project_id to null
        if (array_key_exists('project_id', $data) && $data['project_id'] === '') {
            $data['project_id'] = null;
        }
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
     * Increase task priority by one level (Low → Medium → High → Critical).
     * Returns false if already at maximum.
     */
    public function increasePriority(Task $task): bool
    {
        $idx = array_search($task->priority, self::PRIORITIES);
        if ($idx === false || $idx >= count(self::PRIORITIES) - 1) {
            return false;
        }
        $task->update(['priority' => self::PRIORITIES[$idx + 1]]);
        return true;
    }

    /**
     * Decrease task priority by one level (Critical → High → Medium → Low).
     * Returns false if already at minimum.
     */
    public function decreasePriority(Task $task): bool
    {
        $idx = array_search($task->priority, self::PRIORITIES);
        if ($idx === false || $idx <= 0) {
            return false;
        }
        $task->update(['priority' => self::PRIORITIES[$idx - 1]]);
        return true;
    }

    /**
     * Delete a task.
     */
    public function deleteTask(Task $task): bool
    {
        return $task->delete();
    }
}
