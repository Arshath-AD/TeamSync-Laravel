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
        $query = Task::with(['project', 'assignee']);

        if ($request) {
            $query = $this->applyFilters($query, $request);
        }

        return $query->orderByRaw('FIELD(priority, "Critical", "High", "Medium", "Low")')
            ->orderBy('deadline', 'asc')
            ->get();
    }

    /**
     * Get paginated data for the main tasks index (both board and list views).
     */
    public function getIndexData(Request $request): array
    {
        $baseQuery = Task::with(['project', 'assignee']);
        $baseQuery = $this->applyFilters($baseQuery, $request);

        $listTasks = (clone $baseQuery)
            ->orderByRaw('FIELD(priority, "Critical", "High", "Medium", "Low")')
            ->orderBy('deadline', 'asc')
            ->paginate(15, ['*'], 'page')->withQueryString();

        $board = [
            'Todo'        => (clone $baseQuery)->where('status', 'Pending')->orderByRaw('FIELD(priority, "Critical", "High", "Medium", "Low")')->orderBy('deadline', 'asc')->paginate(5, ['*'], 'todo_page')->withQueryString(),
            'In Progress' => (clone $baseQuery)->where('status', 'In Progress')->orderByRaw('FIELD(priority, "Critical", "High", "Medium", "Low")')->orderBy('deadline', 'asc')->paginate(5, ['*'], 'progress_page')->withQueryString(),
            'On Hold'     => (clone $baseQuery)->where('status', 'On Hold')->orderByRaw('FIELD(priority, "Critical", "High", "Medium", "Low")')->orderBy('deadline', 'asc')->paginate(5, ['*'], 'hold_page')->withQueryString(),
            'Completed'   => (clone $baseQuery)->where('status', 'Completed')->orderByRaw('FIELD(priority, "Critical", "High", "Medium", "Low")')->orderBy('deadline', 'asc')->paginate(5, ['*'], 'completed_page')->withQueryString(),
        ];

        $overdueCount = (clone $baseQuery)
            ->whereNotIn('status', ['Completed', 'On Hold'])
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<', now()->startOfDay())
            ->count();

        return [
            'tasks' => $listTasks,
            'board' => $board,
            'stats' => [
                'total'       => $listTasks->total(),
                'active'      => $board['Todo']->total() + $board['In Progress']->total(),
                'completed'   => $board['Completed']->total(),
                'overdue'     => $overdueCount,
            ]
        ];
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
        $query = Task::with(['project'])
            ->where('assigned_to', $user->id);

        if ($request) {
            $query = $this->applyFilters($query, $request);
        }

        $tasks = $query->orderByRaw('FIELD(priority, "Critical", "High", "Medium", "Low")')
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
     * Apply filters to the task query based on the request.
     */
    private function applyFilters($query, Request $request)
    {
        if ($request->has('priority')) {
            $priorities = (array) $request->input('priority');
            $query->where(function ($q) use ($priorities) {
                $realPriorities = array_intersect($priorities, ['High', 'Medium', 'Low', 'Critical']);
                $hasOverdue = in_array('Overdue', $priorities);

                if (!empty($realPriorities) && $hasOverdue) {
                    $q->whereIn('priority', $realPriorities)
                      ->orWhere(function ($sub) {
                          $sub->whereNotIn('status', ['Completed', 'On Hold'])
                              ->whereNotNull('deadline')
                              ->whereDate('deadline', '<', now()->startOfDay());
                      });
                } elseif (!empty($realPriorities)) {
                    $q->whereIn('priority', $realPriorities);
                } elseif ($hasOverdue) {
                    $q->whereNotIn('status', ['Completed', 'On Hold'])
                      ->whereNotNull('deadline')
                      ->whereDate('deadline', '<', now()->startOfDay());
                }
            });
        }

        if ($request->filled('assignee')) {
            $query->where('assigned_to', $request->input('assignee'));
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'Todo') {
                $status = 'Pending';
            }
            $query->where('status', $status);
        }

        if ($request->filled('due_date')) {
            $query->whereDate('deadline', $request->input('due_date'));
        }

        return $query;
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
