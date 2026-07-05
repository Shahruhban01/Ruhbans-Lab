<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Core\Application;
use App\Repositories\MembershipRepository;
use App\Repositories\UserRepository;

final class MembershipService
{
    private Application $app;
    private MembershipRepository $membershipRepository;
    private UserRepository $userRepository;

    public function __construct(Application $app, MembershipRepository $membershipRepository, UserRepository $userRepository)
    {
        $this->app = $app;
        $this->membershipRepository = $membershipRepository;
        $this->userRepository = $userRepository;
    }

    public function getMembershipsList(int $page = 1, int $perPage = 15): array
    {
        return $this->membershipRepository->getMembershipsList($page, $perPage);
    }

    public function plans(): array
    {
        return $this->membershipRepository->allPlans();
    }

    public function assignPlan(int $userId, int $planId, ?string $endsAt = null): bool
    {
        if ($endsAt !== null) {
            $endsAt = trim($endsAt);
            if ($endsAt === '') {
                $endsAt = null;
            }
        }
        return $this->membershipRepository->assignPlan($userId, $planId, $endsAt);
    }

    public function cancelMembership(int $userId): bool
    {
        return $this->membershipRepository->cancelMembership($userId);
    }

    public function checkAccess(array $membership, string $requiredFeature): bool
    {
        $features = isset($membership['features']) && is_array($membership['features']) ? $membership['features'] : array();
        return in_array($requiredFeature, $features, true);
    }
}
