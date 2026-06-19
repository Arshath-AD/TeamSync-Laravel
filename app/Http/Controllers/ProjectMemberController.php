<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Services\ProjectMemberService;
use App\Http\Requests\StoreProjectMemberRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProjectMemberController extends Controller
{
    protected $memberService;

    public function __construct(ProjectMemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    /**
     * Redirect to the workspace members tab.
     */
    public function index(Project $project)
    {
        return redirect()->route('projects.show', [$project, 'tab' => 'members']);
    }

    /**
     * Assign a new member to the project.
     */
    public function store(StoreProjectMemberRequest $request, Project $project)
    {
        try {
            $this->memberService->assignMember($project, $request->validated('user_id'));
            
            return redirect()->route('projects.show', [$project, 'tab' => 'members'])
                ->with('success', 'Member assigned successfully.');
        } catch (ValidationException $e) {
            return redirect()->route('projects.show', [$project, 'tab' => 'members'])
                ->withErrors($e->errors())
                ->with('error', collect($e->errors())->first()[0] ?? 'Cannot assign member.');
        }
    }

    /**
     * Remove a member from the project.
     */
    public function destroy(Project $project, User $user)
    {
        try {
            $this->memberService->removeMember($project, $user);
            
            return redirect()->route('projects.show', [$project, 'tab' => 'members'])
                ->with('success', 'Member removed successfully.');
        } catch (ValidationException $e) {
            return redirect()->route('projects.show', [$project, 'tab' => 'members'])
                ->withErrors($e->errors())
                ->with('error', collect($e->errors())->first()[0] ?? 'Cannot remove member.');
        }
    }
}
