<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use App\Services\ProjectService;

$taskService = app(TaskService::class);
$projectService = app(ProjectService::class);

$user = User::first();
$project = Project::first();

echo "========================================\n";
echo "1. TASK CREATION\n";
echo "========================================\n";
$task = $taskService->createTask([
    'task_name' => 'Verify Kanban Board',
    'description' => 'Ensure the drag and drop status works.',
    'project_id' => $project->id,
    'assigned_to' => $user->id,
    'status' => 'Pending',
    'priority' => 'High',
    'deadline' => now()->addDays(2)->format('Y-m-d')
]);
echo "Created Task: [{$task->id}] {$task->task_name} (Status: {$task->status})\n\n";

echo "========================================\n";
echo "2. BOARD VIEW (Global)\n";
echo "========================================\n";
$board = $taskService->getBoardView();
echo "Todo count: " . count($board['Todo']) . "\n";
echo "In Progress count: " . count($board['In Progress']) . "\n";
echo "Completed count: " . count($board['Completed']) . "\n\n";

echo "========================================\n";
echo "3. USER 'MY TASKS' PAGE\n";
echo "========================================\n";
// Create an overdue task to demonstrate highlighting
$overdueTask = $taskService->createTask([
    'task_name' => 'Overdue Task Demo',
    'project_id' => $project->id,
    'assigned_to' => $user->id,
    'status' => 'In Progress',
    'priority' => 'Critical',
    'deadline' => now()->subDays(2)->format('Y-m-d')
]);
$myTasks = $taskService->getUserTasks($user);
echo "Total Assigned Tasks: " . count($myTasks['tasks']) . "\n";
echo "Overdue Tasks: " . count($myTasks['overdue']) . "\n";
echo "Upcoming Tasks: " . count($myTasks['upcoming']) . "\n\n";

echo "========================================\n";
echo "4. STATUS UPDATE & PROJECT METRICS\n";
echo "========================================\n";
$metricsBefore = $projectService->getProjectMetrics($project);
echo "Project Completion Before: {$metricsBefore['completion_percentage']}%\n";

$taskService->updateStatus($task, 'Completed');
$taskService->updateStatus($overdueTask, 'Completed');
echo "Updated both tasks to 'Completed'.\n";

$metricsAfter = $projectService->getProjectMetrics($project);
echo "Project Completion After: {$metricsAfter['completion_percentage']}%\n\n";

echo "========================================\n";
echo "5. TASK EDITING & DELETION\n";
echo "========================================\n";
$taskService->updateTask($task, ['task_name' => 'Verify Kanban Board (Updated)']);
echo "Updated Task Name: {$task->task_name}\n";

$taskService->deleteTask($task);
$taskService->deleteTask($overdueTask);
echo "Tasks deleted successfully.\n";
