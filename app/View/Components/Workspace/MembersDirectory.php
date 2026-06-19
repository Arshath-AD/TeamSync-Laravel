<?php

namespace App\View\Components\Workspace;

use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MembersDirectory extends Component
{
    public $members;

    public function __construct()
    {
        $this->members = User::withCount([
            'tasks as total_tasks',
            'tasks as completed_tasks' => fn ($q) => $q->where('status', 'Completed'),
            'memberOfProjects as assigned_projects',
            'ledProjects as led_projects_count',
        ])->get()->map(function ($user) {
            $user->active_tasks = $user->total_tasks - $user->completed_tasks;
            $user->project_count = $user->assigned_projects + $user->led_projects_count;

            return $user;
        })->sortByDesc('active_tasks')->values();
    }

    public function render(): View|Closure|string
    {
        return view('components.workspace.members-directory');
    }
}
