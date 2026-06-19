<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Services\ProjectService;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = $this->projectService->getAllProjects();
        
        // Append metrics to each project for the view
        foreach ($projects as $project) {
            $project->metrics = $this->projectService->getProjectMetrics($project);
        }

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('projects.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $this->projectService->createProject($request->validated());

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource (Workspace).
     */
    public function show(Request $request, Project $project)
    {
        $allowedTabs = ['overview', 'tasks', 'members', 'activity'];
        $tab = $request->query('tab', 'overview');
        
        if (!in_array($tab, $allowedTabs)) {
            $tab = 'overview';
        }

        $workspaceData = $this->projectService->getProjectWorkspace($project, $tab);

        return view('projects.show', $workspaceData);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $users = User::orderBy('name')->get();
        return view('projects.edit', compact('project', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->projectService->updateProject($project, $request->validated());

        return redirect()->route('projects.show', [$project, 'tab' => 'overview'])
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        try {
            $this->projectService->deleteProject($project);
            return redirect()->route('projects.index')
                ->with('success', 'Project deleted successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->with('error', 'Cannot delete project.');
        }
    }
}
