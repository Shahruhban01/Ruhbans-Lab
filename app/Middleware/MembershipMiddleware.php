<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Application;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\MembershipRepository;

final class MembershipMiddleware implements MiddlewareInterface
{
    private Application $app;
    private MembershipRepository $membershipRepository;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->membershipRepository = new MembershipRepository($this->app->database()->connection());
    }

    public function handle(Request $request, callable $next)
    {
        $user = $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user'));

        if (!is_array($user) || !isset($user['id'])) {
            // Unauthenticated users fall back to standard access controls (Free level features)
            $request->set('membership', array(
                'plan_slug' => 'free',
                'plan_name' => 'Free',
                'features' => array('read_general')
            ));
            return $next();
        }

        $activeMembership = $this->membershipRepository->getActiveMembership((int)$user['id']);

        if (!$activeMembership) {
            // Default active member setting if no plan assigned yet
            $freePlan = $this->membershipRepository->findPlanBySlug('free');
            if ($freePlan) {
                $this->membershipRepository->assignPlan((int)$user['id'], (int)$freePlan['id']);
                $activeMembership = $this->membershipRepository->getActiveMembership((int)$user['id']);
            }
        }

        if ($activeMembership) {
            $features = json_decode((string)($activeMembership['features'] ?? '[]'), true);
            if (!is_array($features)) {
                $features = array();
            }
            $request->set('membership', array(
                'plan_slug' => $activeMembership['plan_slug'],
                'plan_name' => $activeMembership['plan_name'],
                'features' => $features,
                'ends_at' => $activeMembership['ends_at']
            ));
        }

        return $next();
    }
}
