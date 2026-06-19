<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Handle the incoming request to the dashboard.
     */
    public function index()
    {
        \Illuminate\Support\Facades\Log::info('DashboardController@index started');
        $user = Auth::user();

        if ($user->role === 'admin') {
            $data = $this->dashboardService->getAdminDashboard();
            $viewData = array_merge(['user' => $user], $data);
            \Illuminate\Support\Facades\Log::info('DashboardController@index finished (admin)');
            return view('dashboard', $viewData);
        }

        // Standard user dashboard
        $data = $this->dashboardService->getUserDashboard($user);
        $viewData = array_merge(['user' => $user], $data);
        \Illuminate\Support\Facades\Log::info('DashboardController@index finished');
        return view('dashboard', $viewData);
    }
}
