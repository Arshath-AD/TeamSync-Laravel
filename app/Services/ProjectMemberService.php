<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use App\Models\Task;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class ProjectMemberService
{
    /**
     * Assign a user to a project.
     * Prevents duplicate assignments.
     */
    public function assignMember(Project $project, int $userId): void
    {
        if ($project->members()->where('user_id', $userId)->exists()) {
            throw ValidationException::withMessages([
                'user_id' => 'This user is already a member of the project.',
            ]);
        }

        $project->members()->attach($userId);
    }

    /**
     * Remove a member from a project.
     * Enforces Lead protection and Active Task protection.
     */
    public function removeMember(Project $project, User $user): void
    {
        // Lead Protection
        if ($project->project_lead_id === $user->id) {
            throw ValidationException::withMessages([
                'user' => 'Cannot remove the Project Lead from the project members.',
            ]);
        }

        // Active Task Protection
        $activeTasks = Task::where('project_id', $project->id)
            ->where('assigned_to', $user->id)
            ->where('status', '!=', 'Completed')
            ->count();

        if ($activeTasks > 0) {
            throw ValidationException::withMessages([
                'user' => 'Cannot remove a member who has active tasks in this project. Please reassign their tasks first.',
            ]);
        }

        $project->members()->detach($user->id);
    }

    /**
     * Get task metrics for a specific user across all projects.
     * Capacity metric preparation.
     */
    public function getAssignedTaskCount(User $user): array
    {
        $tasks = Task::where('assigned_to', $user->id)->get();
        
        $total = $tasks->count();
        $completed = $tasks->where('status', 'Completed')->count();
        $active = $total - $completed;

        return [
            'total_tasks' => $total,
            'active_tasks' => $active,
            'completed_tasks' => $completed,
        ];
    }

    /**
     * Get workload metrics for all members of a specific project.
     * Capacity metric preparation.
     */
    public function getProjectWorkload(Project $project): array
    {
        // Get all members, and explicitly ensure the lead is included even if pivot missing
        $members = $project->members->keyBy('id');
        if ($project->lead && !$members->has($project->lead->id)) {
            $members->put($project->lead->id, $project->lead);
        }

        $workload = [];

        foreach ($members as $member) {
            $tasks = $project->tasks->where('assigned_to', $member->id);
            $total = $tasks->count();
            $completed = $tasks->where('status', 'Completed')->count();
            
            $workload[$member->id] = [
                'user' => $member,
                'assigned_tasks' => $total,
                'active_tasks' => $total - $completed,
                'completed_tasks' => $completed,
            ];
        }

        return $workload;
    }
}
