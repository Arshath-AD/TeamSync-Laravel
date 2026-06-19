<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Get the aggregated metrics and widgets for the Admin Dashboard.
     */
    public function getAdminDashboard(): array
    {
        $today = Carbon::today();

        // Top-level KPIs
        $totalProjects = Project::count();
        $activeProjects = Project::whereHas('tasks', function ($query) {
            $query->where('status', '!=', 'Completed');
        })->count();
        $totalUsers = User::count();

        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'Completed')->count();
        $activeTasks = $totalTasks - $completedTasks;
        $overdueTasks = Task::whereNotIn('status', ['Completed', 'On Hold'])
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<', $today)
            ->count();

        // Widgets
        $recentProjects = Project::with('lead')->latest()->limit(5)->get();
        $upcomingDeadlines = Task::with(['project', 'assignee'])
            ->where('status', '!=', 'Completed')
            ->whereNotNull('deadline')
            ->whereDate('deadline', '>=', $today)
            ->orderBy('deadline', 'asc')
            ->limit(5)
            ->get();

        // Aggregated Capacity Overview
        $capacityOverview = User::with(['tasks' => function($query) {
            $query->where('status', '!=', 'Completed');
        }])->withCount([
            'tasks as total_tasks',
            'tasks as completed_tasks' => function ($query) {
                $query->where('status', 'Completed');
            }
        ])->get()->map(function ($user) {
            $user->active_tasks = $user->total_tasks - $user->completed_tasks;
            
            $user->weighted_workload = $user->tasks->reduce(function($carry, $task) {
                $weight = match($task->priority) {
                    'Critical' => 5,
                    'High'     => 3,
                    'Medium'   => 2,
                    'Low'      => 1,
                    default    => 2,
                };
                return $carry + $weight;
            }, 0);

            return $user;
        })->sortByDesc('weighted_workload')->values();

        return [
            'metrics' => [
                'total_projects' => $totalProjects,
                'active_projects' => $activeProjects,
                'total_users' => $totalUsers,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'active_tasks' => $activeTasks,
                'overdue_tasks' => $overdueTasks,
            ],
            'widgets' => [
                'recent_projects' => $recentProjects,
                'upcoming_deadlines' => $upcomingDeadlines,
                'capacity_overview' => $capacityOverview,
            ],
        ];
    }

    /**
     * Get personalized aggregated metrics and widgets for the User Dashboard.
     */
    public function getUserDashboard(User $user): array
    {
        $today = Carbon::today();

        // Personal KPIs
        $totalTasks = Task::where('assigned_to', $user->id)->count();
        $completedTasks = Task::where('assigned_to', $user->id)->where('status', 'Completed')->count();
        $activeTasks = $totalTasks - $completedTasks;
        $completionPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        $dueToday = Task::where('assigned_to', $user->id)
            ->where('status', '!=', 'Completed')
            ->whereNotNull('deadline')
            ->whereDate('deadline', '=', $today)
            ->count();

        $overdueTasks = Task::where('assigned_to', $user->id)
            ->whereNotIn('status', ['Completed', 'On Hold'])
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<', $today)
            ->count();

        $assignedProjects = Project::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->orWhere('project_lead_id', $user->id)->count();

        $myActiveProjects = Project::whereHas('tasks', function ($query) use ($user) {
            $query->where('assigned_to', $user->id)
                  ->where('status', '!=', 'Completed');
        })->count();

        // Personal Widgets
        $upcomingDeadlines = Task::with('project')
            ->where('assigned_to', $user->id)
            ->where('status', '!=', 'Completed')
            ->whereNotNull('deadline')
            ->whereDate('deadline', '>=', $today)
            ->orderBy('deadline', 'asc')
            ->limit(5)
            ->get();

        $myActiveTasksList = Task::with('project')
            ->where('assigned_to', $user->id)
            ->where('status', '!=', 'Completed')
            ->orderByRaw('FIELD(priority, "Critical", "High", "Medium", "Low")')
            ->orderBy('deadline', 'asc')
            ->limit(5)
            ->get();

        return [
            'metrics' => [
                'total_tasks' => $totalTasks,
                'active_tasks' => $activeTasks,
                'completed_tasks' => $completedTasks,
                'completion_percentage' => $completionPercentage,
                'due_today' => $dueToday,
                'overdue_tasks' => $overdueTasks,
                'assigned_projects' => $assignedProjects,
                'my_active_projects' => $myActiveProjects,
            ],
            'widgets' => [
                'upcoming_deadlines' => $upcomingDeadlines,
                'my_active_tasks' => $myActiveTasksList,
            ],
        ];
    }
}
