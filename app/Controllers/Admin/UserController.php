<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Repositories\AuditLogRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Core\Request;
use App\Services\Admin\UserService;

final class UserController extends BaseAdminController
{
    private UserService $userService;

    public function __construct(\App\Core\Application $app)
    {
        parent::__construct($app);
        $connection = $this->app->database()->connection();
        $userRepository = new UserRepository($connection);
        $roleRepository = new RoleRepository($connection);
        $auditLogRepository = new AuditLogRepository($connection);

        $this->userService = new UserService($this->app, $userRepository, $roleRepository, $auditLogRepository);
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $page = (int) $request->input('page', 1);

        return $this->adminView('admin/users/index', array(
            'users' => $this->userService->listUsers($search, $page, 15),
            'roles' => $this->userService->roles(),
            'search' => $search,
            'currentUser' => $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user')),
        ), array(
            'title' => 'User Management',
            'description' => 'Manage users, roles, and activation status.',
            'canonical' => url('/admin/users'),
            'robots' => 'noindex, nofollow',
        ));
    }

    public function updateRole(Request $request)
    {
        $userId = (int) $request->input('user_id', 0);
        $roleId = (int) $request->input('role_id', 0);

        if ($userId <= 0 || $roleId <= 0) {
            $this->app->session()->flash('error', 'Invalid role update request.');

            return $this->redirect('/admin/users');
        }

        $this->userService->updateRole($userId, $roleId);
        $this->app->session()->flash('success', 'User role updated successfully.');

        return $this->redirect('/admin/users');
    }

    public function toggleStatus(Request $request)
    {
        $userId = (int) $request->input('user_id', 0);
        $isActive = (int) $request->input('is_active', 0);

        if ($userId <= 0) {
            $this->app->session()->flash('error', 'Invalid status update request.');

            return $this->redirect('/admin/users');
        }

        $this->userService->toggleStatus($userId, $isActive ? 1 : 0);
        $this->app->session()->flash('success', 'User status updated successfully.');

        return $this->redirect('/admin/users');
    }
}
