<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Core\Application;
use App\Repositories\AuditLogRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\BaseService;

final class UserService extends BaseService
{
    private Application $app;
    private RoleRepository $roleRepository;
    private AuditLogRepository $auditLogRepository;

    public function __construct(Application $app, UserRepository $userRepository, RoleRepository $roleRepository, AuditLogRepository $auditLogRepository)
    {
        parent::__construct($userRepository);
        $this->app = $app;
        $this->roleRepository = $roleRepository;
        $this->auditLogRepository = $auditLogRepository;
    }

    public function listUsers(string $search = '', int $page = 1, int $perPage = 15): array
    {
        return $this->repository->paginateWithRoles($search, $page, $perPage);
    }

    public function roles(): array
    {
        return $this->roleRepository->allRoles();
    }

    public function updateRole($userId, $roleId): bool
    {
        $updated = $this->repository->updateRole($userId, $roleId);

        if ($updated) {
            $currentUser = $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user'));
            $this->auditLogRepository->createLog(isset($currentUser['id']) ? (int) $currentUser['id'] : null, 'user_role_updated', 'Updated user role.', array('user_id' => $userId, 'role_id' => $roleId), $this->app->request()->ip(), (string) $this->app->request()->header('User-Agent', ''));
        }

        return $updated;
    }

    public function toggleStatus($userId, int $isActive): bool
    {
        $updated = $this->repository->updateStatus($userId, $isActive);

        if ($updated) {
            $currentUser = $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user'));
            $this->auditLogRepository->createLog(isset($currentUser['id']) ? (int) $currentUser['id'] : null, 'user_status_updated', 'Updated user status.', array('user_id' => $userId, 'is_active' => $isActive), $this->app->request()->ip(), (string) $this->app->request()->header('User-Agent', ''));
        }

        return $updated;
    }
}
