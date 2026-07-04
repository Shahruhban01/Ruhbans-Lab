<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Repositories\AuditLogRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Core\Request;
use App\Services\Admin\DashboardService;

final class DashboardController extends BaseAdminController
{
    private DashboardService $dashboardService;

    public function __construct(\App\Core\Application $app)
    {
        parent::__construct($app);
        $connection = $this->app->database()->connection();
        $userRepository = new UserRepository($connection);
        $auditLogRepository = new AuditLogRepository($connection);
        $roleRepository = new RoleRepository($connection);

        $this->dashboardService = new DashboardService($this->app, $userRepository, $auditLogRepository, $roleRepository);
    }

    public function index(Request $request)
    {
        return $this->adminView('admin/dashboard/index', array(
            'stats' => $this->dashboardService->dashboardStats(),
            'recentActivity' => $this->dashboardService->recentActivity(8),
            'recentUsers' => $this->dashboardService->recentUsers(5),
            'currentUser' => $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user')),
        ), array(
            'title' => 'Dashboard',
            'description' => 'Admin dashboard overview for Developer Ruhban.',
            'canonical' => url('/admin'),
            'robots' => 'noindex, nofollow',
        ));
    }

    public function activityLogs(Request $request)
    {
        return $this->adminView('admin/activity/index', array(
            'logs' => $this->dashboardService->recentActivity(50),
            'currentUser' => $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user')),
        ), array(
            'title' => 'Activity Logs',
            'description' => 'Recent administrative activity and audit history.',
            'canonical' => url('/admin/activity-logs'),
            'robots' => 'noindex, nofollow',
        ));
    }
}
