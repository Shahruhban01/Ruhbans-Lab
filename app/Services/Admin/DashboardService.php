<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Core\Application;
use App\Repositories\AuditLogRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\BaseService;

final class DashboardService extends BaseService
{
    private Application $app;
    private AuditLogRepository $auditLogRepository;
    private RoleRepository $roleRepository;

    public function __construct(Application $app, UserRepository $userRepository, AuditLogRepository $auditLogRepository, RoleRepository $roleRepository)
    {
        parent::__construct($userRepository);
        $this->app = $app;
        $this->auditLogRepository = $auditLogRepository;
        $this->roleRepository = $roleRepository;
    }

    public function dashboardStats(): array
    {
        return array(
            array('label' => 'Total Users', 'value' => $this->repository->countTotal(), 'note' => 'All registered accounts'),
            array('label' => 'Active Users', 'value' => $this->repository->countActive(), 'note' => 'Currently active accounts'),
            array('label' => 'Admins', 'value' => $this->repository->countByRoleSlug('admin'), 'note' => 'Privileged accounts'),
            array('label' => 'Today\'s Logs', 'value' => $this->auditLogRepository->countToday(), 'note' => 'Recent admin activity'),
        );
    }

    public function recentActivity(int $limit = 10): array
    {
        return $this->auditLogRepository->recent($limit);
    }

    public function recentUsers(int $limit = 5): array
    {
        return $this->repository->recent($limit);
    }

    public function roles(): array
    {
        return $this->roleRepository->allRoles();
    }
}
