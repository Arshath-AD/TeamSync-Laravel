<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Services\ProjectService;
use App\Services\ProjectMemberService;
use App\Services\TaskService;
use Illuminate\Validation\ValidationException;

$projectService = app(ProjectService::class);
$memberService = app(ProjectMemberService::class);
$taskService = app(TaskService::class);

$leadUser = User::firstOrCreate(['email' => 'lead@example.com'], ['name' => 'Lead User', 'password' => 'password', 'role' => 'admin']);
$activeMemberUser = User::firstOrCreate(['email' => 'active@example.com'], ['name' => 'Active Member', 'password' => 'password', 'role' => 'user']);
$idleMemberUser = User::firstOrCreate(['email' => 'idle@example.com'], ['name' => 'Idle Member', 'password' => 'password', 'role' => 'user']);

echo "========================================\n";
echo "1. PROJECT LEAD AS MEMBER\n";
echo "========================================\n";
$project = $projectService->createProject([
    'project_name' => 'Phase 5 Validation',
    'description' => 'Testing members module',
    'project_lead_id' => $leadUser->id,
]);
echo "Created Project '{$project->project_name}' with Lead '{$leadUser->name}'.\n";

$isMember = $project->members()->where('user_id', $leadUser->id)->exists();
echo "Is Lead automatically in project_members table? " . ($isMember ? "YES" : "NO") . "\n\n";

echo "========================================\n";
echo "2. DUPLICATE ASSIGNMENT BLOCKED\n";
echo "========================================\n";
try {
    $memberService->assignMember($project, $leadUser->id);
    echo "ERROR: Duplicate assignment succeeded!\n";
} catch (ValidationException $e) {
    echo "SUCCESS: Duplicate assignment blocked. Message:\n";
    echo $e->getMessage() . "\n\n";
}

echo "========================================\n";
echo "3. PROJECT LEAD REMOVAL BLOCKED\n";
echo "========================================\n";
try {
    $memberService->removeMember($project, $leadUser);
    echo "ERROR: Lead removal succeeded!\n";
} catch (ValidationException $e) {
    echo "SUCCESS: Lead removal blocked. Message:\n";
    echo $e->getMessage() . "\n\n";
}

echo "========================================\n";
echo "4. ACTIVE-TASK MEMBER REMOVAL BLOCKED\n";
echo "========================================\n";
// Assign a new member
$memberService->assignMember($project, $activeMemberUser->id);
// Assign them an active task
$task = $taskService->createTask([
    'task_name' => 'Block Removal Task',
    'project_id' => $project->id,
    'assigned_to' => $activeMemberUser->id,
    'status' => 'In Progress',
    'priority' => 'High',
    'deadline' => now()->addDays(5)->format('Y-m-d')
]);

try {
    $memberService->removeMember($project, $activeMemberUser);
    echo "ERROR: Active-task member removal succeeded!\n";
} catch (ValidationException $e) {
    echo "SUCCESS: Active-task member removal blocked. Message:\n";
    echo $e->getMessage() . "\n\n";
}

echo "========================================\n";
echo "5. IDLE MEMBER REMOVAL SUCCEEDS\n";
echo "========================================\n";
$memberService->assignMember($project, $idleMemberUser->id);
echo "Assigned idle member '{$idleMemberUser->name}'.\n";
$memberService->removeMember($project, $idleMemberUser);
echo "SUCCESS: Removed idle member '{$idleMemberUser->name}'.\n\n";

echo "========================================\n";
echo "6. WORKLOAD METRICS & CAPACITY PREPARATION\n";
echo "========================================\n";
$workload = $memberService->getProjectWorkload($project);
echo "Project Workload Breakdown:\n";
foreach ($workload as $data) {
    echo "- {$data['user']->name}: Total Tasks: {$data['assigned_tasks']}, Active: {$data['active_tasks']}, Completed: {$data['completed_tasks']}\n";
}

$userTaskCount = $memberService->getAssignedTaskCount($activeMemberUser);
echo "\nGlobal User Workload for '{$activeMemberUser->name}':\n";
echo "- Total Tasks: {$userTaskCount['total_tasks']}\n";
echo "- Active Tasks: {$userTaskCount['active_tasks']}\n";
echo "- Completed Tasks: {$userTaskCount['completed_tasks']}\n\n";

echo "========================================\n";
echo "7. CLEANUP\n";
echo "========================================\n";
$task->delete();
$projectService->deleteProject($project);
echo "Deleted test project and task.\n";
