<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Services\DashboardService;
use Carbon\Carbon;

$dashboardService = app(DashboardService::class);

$admin = User::where('role', 'admin')->first();
$standardUser = User::where('role', '!=', 'admin')->first() ?? $admin;

echo "========================================\n";
echo "1. ADMIN DASHBOARD METRICS\n";
echo "========================================\n";
$adminData = $dashboardService->getAdminDashboard();
$metrics = $adminData['metrics'];
echo "Total Projects: {$metrics['total_projects']}\n";
echo "Active Projects: {$metrics['active_projects']}\n";
echo "Total Users: {$metrics['total_users']}\n";
echo "Total Tasks: {$metrics['total_tasks']}\n";
echo "Completed Tasks: {$metrics['completed_tasks']}\n";
echo "Active Tasks: {$metrics['active_tasks']}\n";
echo "Overdue Tasks: {$metrics['overdue_tasks']}\n\n";

echo "========================================\n";
echo "2. ADMIN CAPACITY OVERVIEW\n";
echo "========================================\n";
$capacity = $adminData['widgets']['capacity_overview'];
echo sprintf("%-20s | %-10s | %-10s | %-10s\n", "User", "Active", "Completed", "Total");
echo str_repeat("-", 58) . "\n";
foreach ($capacity as $stat) {
    echo sprintf("%-20s | %-10d | %-10d | %-10d\n", substr($stat->name, 0, 20), $stat->active_tasks, $stat->completed_tasks, $stat->total_tasks);
}
echo "\n";

echo "========================================\n";
echo "3. USER DASHBOARD METRICS\n";
echo "========================================\n";
echo "User: {$standardUser->name}\n";
$userData = $dashboardService->getUserDashboard($standardUser);
$userMetrics = $userData['metrics'];
echo "Total Tasks: {$userMetrics['total_tasks']}\n";
echo "Active Tasks: {$userMetrics['active_tasks']}\n";
echo "Completed Tasks: {$userMetrics['completed_tasks']}\n";
echo "Completion Rate: {$userMetrics['completion_percentage']}%\n";
echo "Due Today: {$userMetrics['due_today']}\n";
echo "Overdue Tasks: {$userMetrics['overdue_tasks']}\n";
echo "Assigned Projects: {$userMetrics['assigned_projects']}\n";
echo "My Active Projects: {$userMetrics['my_active_projects']}\n\n";

echo "Verification Complete.\n";
