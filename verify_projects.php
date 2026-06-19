<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Services\ProjectService;

$service = app(ProjectService::class);
$user = User::first();

echo "========================================\n";
echo "1. PROJECT CREATION\n";
echo "========================================\n";
$project = $service->createProject([
    'project_name' => 'Automated Verification Project',
    'description' => 'Testing the Service Layer',
    'project_lead_id' => $user->id,
]);
echo "Created Project ID: {$project->id} - {$project->project_name}\n\n";

echo "========================================\n";
echo "2. PROJECT EDITING\n";
echo "========================================\n";
$project = $service->updateProject($project, [
    'project_name' => 'Updated Verification Project'
]);
echo "Updated Project Name: {$project->project_name}\n\n";

echo "========================================\n";
echo "3. WORKSPACE DATA & METRICS\n";
echo "========================================\n";
// Add a fake task and member to test metrics
$task = Task::create([
    'task_name' => 'Dummy Task',
    'description' => 'Test',
    'assigned_to' => $user->id,
    'project_id' => $project->id,
    'status' => 'Completed',
    'priority' => 'Medium'
]);
$project->members()->attach($user->id);

$workspace = $service->getProjectWorkspace($project, 'overview');
echo "Active Tab: {$workspace['activeTab']}\n";
echo "Member Count: {$workspace['metrics']['member_count']}\n";
echo "Task Count: {$workspace['metrics']['task_count']}\n";
echo "Completed Tasks: {$workspace['metrics']['completed_task_count']}\n";
echo "Completion Percentage: {$workspace['metrics']['completion_percentage']}%\n\n";

echo "========================================\n";
echo "4. DELETE PROTECTION\n";
echo "========================================\n";
try {
    $service->deleteProject($project);
    echo "ERROR: Project was deleted despite having tasks!\n";
} catch (\Illuminate\Validation\ValidationException $e) {
    echo "SUCCESS: Caught ValidationException during delete:\n";
    echo $e->getMessage() . "\n\n";
}

echo "========================================\n";
echo "5. CLEANUP\n";
echo "========================================\n";
$task->delete(); // Delete task first to allow project deletion
$service->deleteProject($project); // Now delete project
echo "Cleanup successful. Deleted verification project and task.\n";
