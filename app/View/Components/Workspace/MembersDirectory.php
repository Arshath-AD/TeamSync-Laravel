<?php

namespace App\View\Components\Workspace;

use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class MembersDirectory extends Component
{
    public $members;
    public bool $isAdmin;
    public string $search;

    public function __construct()
    {
        $this->isAdmin = Auth::user()->role === 'admin';
        $this->search  = request('search', '');

        $query = User::withCount([
            'tasks as total_tasks',
            'tasks as completed_tasks' => fn ($q) => $q->where('status', 'Completed'),
            'memberOfProjects as assigned_projects',
            'ledProjects as led_projects_count',
        ])->with(['tasks' => fn ($q) => $q->where('status', '!=', 'Completed')]);

        // Search support: name, email, role
        if ($this->search) {
            $s = $this->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('role', 'like', "%{$s}%");
            });
        }

        $this->members = $query->get()->map(function ($user) {
            $user->active_tasks    = $user->total_tasks - $user->completed_tasks;
            $user->project_count   = $user->assigned_projects + $user->led_projects_count;
            $user->weighted_workload = $user->tasks->reduce(function ($carry, $task) {
                return $carry + match($task->priority) {
                    'Critical' => 5, 'High' => 3, 'Medium' => 2, 'Low' => 1, default => 2,
                };
            }, 0);
            return $user;
        })->sortByDesc('weighted_workload')->values();
    }

    public function render(): View|Closure|string
    {
        return view('components.workspace.members-directory');
    }
}
