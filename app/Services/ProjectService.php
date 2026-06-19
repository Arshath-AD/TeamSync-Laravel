<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Validation\ValidationException;

class ProjectService
{
    /**
     * Get all projects with relationships eager loaded to prevent N+1 queries.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllProjects()
    {
        return Project::with(['lead'])
            ->withCount([
                'members', 
                'tasks', 
                'tasks as completed_tasks_count' => function ($query) {
                    $query->where('status', 'Completed');
                }
            ])->get();
    }

    /**
     * Calculate metrics for a specific project.
     *
     * @param Project $project
     * @return array
     */
    public function getProjectMetrics(Project $project): array
    {
        // Use withCount() attributes if they exist (index page), 
        // fallback to collection count if models were eagerly loaded (workspace page)
        $memberCount = $project->members_count ?? 
            ($project->relationLoaded('members') ? $project->members->count() : $project->members()->count());
            
        $taskCount = $project->tasks_count ?? 
            ($project->relationLoaded('tasks') ? $project->tasks->count() : $project->tasks()->count());
            
        $completedTaskCount = $project->completed_tasks_count ?? 
            ($project->relationLoaded('tasks') 
                ? $project->tasks->where('status', 'Completed')->count() 
                : $project->tasks()->where('status', 'Completed')->count());
        
        $completionPercentage = $taskCount > 0 
            ? round(($completedTaskCount / $taskCount) * 100) 
            : 0;

        return [
            'member_count' => $memberCount,
            'task_count' => $taskCount,
            'completed_task_count' => $completedTaskCount,
            'completion_percentage' => $completionPercentage,
        ];
    }

    /**
     * Prepare data for the project workspace tab navigation.
     *
     * @param Project $project
     * @param string $tab
     * @return array
     */
    public function getProjectWorkspace(Project $project, string $tab): array
    {
        $project->loadMissing(['lead', 'members', 'tasks']);
        $metrics = $this->getProjectMetrics($project);
        
        $data = [
            'project' => $project,
            'activeTab' => $tab,
            'metrics' => $metrics,
        ];
        
        if ($tab === 'members') {
            $memberService = app(ProjectMemberService::class);
            $data['workload'] = $memberService->getProjectWorkload($project);
            $data['allUsers'] = \App\Models\User::orderBy('name')->get();
        }

        return $data;
    }

    /**
     * Create a new project.
     *
     * @param array $data
     * @return Project
     */
    public function createProject(array $data): Project
    {
        $project = Project::create($data);
        
        // Ensure Project Lead is automatically a member
        if (!empty($data['project_lead_id'])) {
            $project->members()->syncWithoutDetaching([$data['project_lead_id']]);
        }
        
        return $project;
    }

    /**
     * Update an existing project.
     *
     * @param Project $project
     * @param array $data
     * @return Project
     */
    public function updateProject(Project $project, array $data): Project
    {
        $project->update($data);
        
        // Ensure new Project Lead is automatically a member
        if (!empty($data['project_lead_id'])) {
            $project->members()->syncWithoutDetaching([$data['project_lead_id']]);
        }
        
        return $project;
    }

    /**
     * Delete a project with protection against deleting projects with active tasks.
     *
     * @param Project $project
     * @return bool
     * @throws ValidationException
     */
    public function deleteProject(Project $project): bool
    {
        if ($project->tasks()->count() > 0) {
            throw ValidationException::withMessages([
                'project' => 'Cannot delete a project that still contains tasks. Please reassign or delete the tasks first.',
            ]);
        }

        // Clean up project members (though ON DELETE CASCADE is set on the DB, 
        // Eloquent detach is good practice if using relationships)
        $project->members()->detach();

        return $project->delete();
    }
}
