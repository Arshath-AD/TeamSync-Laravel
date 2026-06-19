<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

$dashboardService = app(DashboardService::class);
$admin = User::where('role', 'admin')->first();

Auth::login($admin);

$data = $dashboardService->getAdminDashboard();
$viewData = array_merge(['user' => $admin], $data);

try {
    echo view('dashboard', $viewData)->render();
} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
} catch (\Error $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
