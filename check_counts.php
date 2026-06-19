<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Project;
use Illuminate\Support\Facades\DB;
use App\Services\ProjectService;

$service = app(ProjectService::class);
$projects = $service->getAllProjects();

echo sprintf("%-5s | %-20s | %-10s | %-10s | %-10s | %-10s\n", "ID", "Name", "DB Tasks", "Eloq Tasks", "DB Mems", "Eloq Mems");
echo str_repeat("-", 80) . "\n";

foreach ($projects as $project) {
    $dbTasks = DB::table('tasks')->where('project_id', $project->id)->count();
    $dbMembers = DB::table('project_members')->where('project_id', $project->id)->count();
    
    $metrics = $service->getProjectMetrics($project);
    
    echo sprintf(
        "%-5d | %-20s | %-10d | %-10d | %-10d | %-10d\n",
        $project->id,
        substr($project->project_name, 0, 20),
        $dbTasks,
        $metrics['task_count'],
        $dbMembers,
        $metrics['member_count']
    );
}
