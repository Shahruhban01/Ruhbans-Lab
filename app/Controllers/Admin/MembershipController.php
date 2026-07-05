<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Repositories\AuditLogRepository;
use App\Repositories\MembershipRepository;
use App\Repositories\UserRepository;
use App\Services\Admin\MembershipService;

final class MembershipController extends BaseAdminController
{
    private MembershipService $membershipService;
    private AuditLogRepository $auditLogRepository;
    private UserRepository $userRepository;

    public function __construct(\App\Core\Application $app)
    {
        parent::__construct($app);
        $connection = $this->app->database()->connection();
        $membershipRepository = new MembershipRepository($connection);
        $userRepository = new UserRepository($connection);
        $auditLogRepository = new AuditLogRepository($connection);

        $this->userRepository = $userRepository;
        $this->auditLogRepository = $auditLogRepository;
        $this->membershipService = new MembershipService($this->app, $membershipRepository, $userRepository);
    }

    public function index(Request $request)
    {
        $page = (int) $request->input('page', 1);

        return $this->adminView('admin/memberships/index', array(
            'memberships' => $this->membershipService->getMembershipsList($page, 15),
            'plans' => $this->membershipService->plans(),
            'users' => $this->userRepository->all(),
            'currentUser' => $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user')),
        ), array(
            'title' => 'Membership Administration',
            'description' => 'Assign plans, configure memberships, and inspect subscriptions.',
            'canonical' => url('/admin/memberships'),
            'robots' => 'noindex, nofollow',
        ));
    }

    public function assign(Request $request)
    {
        $userId = (int) $request->input('user_id', 0);
        $planId = (int) $request->input('plan_id', 0);
        $endsAt = $request->input('ends_at', null);

        if ($userId <= 0 || $planId <= 0) {
            $this->app->session()->flash('error', 'Select a valid user and membership plan.');
            return $this->redirect('/admin/memberships');
        }

        $this->membershipService->assignPlan($userId, $planId, $endsAt);
        
        $this->auditLogRepository->createLog(
            $this->currentUserId(),
            'membership_assigned',
            'Updated plan assignment.',
            array('target_user_id' => $userId, 'plan_id' => $planId),
            $request->ip(),
            (string) $request->header('User-Agent', '')
        );

        $this->app->session()->flash('success', 'Membership updated successfully.');
        return $this->redirect('/admin/memberships');
    }

    public function cancel(Request $request)
    {
        $userId = (int) $request->input('user_id', 0);

        if ($userId <= 0) {
            $this->app->session()->flash('error', 'Select a valid user to cancel membership.');
            return $this->redirect('/admin/memberships');
        }

        $this->membershipService->cancelMembership($userId);

        $this->auditLogRepository->createLog(
            $this->currentUserId(),
            'membership_cancelled',
            'Cancelled user membership.',
            array('target_user_id' => $userId),
            $request->ip(),
            (string) $request->header('User-Agent', '')
        );

        $this->app->session()->flash('success', 'Membership cancelled.');
        return $this->redirect('/admin/memberships');
    }

    public function revenue(Request $request)
    {
        $db = $this->app->database()->connection();
        
        // Sum total amount from paid orders
        $stmt = $db->prepare('SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = "paid"');
        $stmt->execute();
        $totalRevenueCents = (int) $stmt->fetchColumn();

        // Get count of active memberships
        $stmt = $db->prepare('SELECT COUNT(*) FROM user_memberships WHERE ends_at IS NULL OR ends_at > NOW()');
        $stmt->execute();
        $activeSubCount = (int) $stmt->fetchColumn();

        // Get count of orders
        $stmt = $db->prepare('SELECT COUNT(*) FROM orders');
        $stmt->execute();
        $ordersCount = (int) $stmt->fetchColumn();

        // Popular premium products downloads count
        $stmt = $db->prepare('
            SELECT p.title, COUNT(ae.id) AS downloads_count
            FROM activity_events ae
            INNER JOIN posts p ON p.id = ae.post_id
            WHERE ae.event_type = "download"
            GROUP BY ae.post_id, p.title
            ORDER BY downloads_count DESC
            LIMIT 5
        ');
        $stmt->execute();
        $popularProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Recent transaction history log
        $stmt = $db->prepare('
            SELECT t.*, u.name AS user_name
            FROM transactions t
            INNER JOIN orders o ON o.id = t.order_id
            INNER JOIN users u ON u.id = o.user_id
            ORDER BY t.created_at DESC
            LIMIT 10
        ');
        $stmt->execute();
        $transactions = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->adminView('admin/memberships/revenue', array(
            'totalRevenue' => $totalRevenueCents / 100,
            'activeSubscriptions' => $activeSubCount,
            'totalOrders' => $ordersCount,
            'popularProducts' => $popularProducts,
            'transactions' => $transactions,
            'currentUser' => $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user')),
        ), array(
            'title' => 'Revenue Dashboard',
            'robots' => 'noindex, nofollow',
        ));
    }

    public function coupons(Request $request)
    {
        $db = $this->app->database()->connection();
        $stmt = $db->prepare('SELECT * FROM coupons ORDER BY created_at DESC');
        $stmt->execute();
        $coupons = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->adminView('admin/memberships/coupons', array(
            'coupons' => $coupons,
            'currentUser' => $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user')),
        ), array(
            'title' => 'Manage Coupons',
            'robots' => 'noindex, nofollow',
        ));
    }

    public function createCoupon(Request $request)
    {
        $code = strtoupper(trim((string) $request->input('code', '')));
        $discount = (int) $request->input('discount_percentage', 0);
        $expiry = $request->input('expires_at', null);

        if ($code === '' || $discount <= 0 || $discount > 100) {
            $this->app->session()->flash('error', 'Provide a valid coupon code and percentage.');
            return $this->redirect('/admin/memberships/coupons');
        }

        $db = $this->app->database()->connection();
        $stmt = $db->prepare('INSERT INTO coupons (code, discount_percentage, expires_at, is_active, created_at, updated_at) VALUES (:code, :discount, :expiry, 1, :created, :updated)');
        $now = date('Y-m-d H:i:s');
        $stmt->execute(array(
            'code' => $code,
            'discount' => $discount,
            'expiry' => !empty($expiry) ? $expiry : null,
            'created' => $now,
            'updated' => $now,
        ));

        $this->app->session()->flash('success', 'Coupon created successfully.');
        return $this->redirect('/admin/memberships/coupons');
    }

    public function deleteCoupon(Request $request, $id)
    {
        $db = $this->app->database()->connection();
        $stmt = $db->prepare('DELETE FROM coupons WHERE id = :id');
        $stmt->execute(array('id' => (int) $id));

        $this->app->session()->flash('success', 'Coupon removed.');
        return $this->redirect('/admin/memberships/coupons');
    }

    public function licenses(Request $request)
    {
        $db = $this->app->database()->connection();
        $stmt = $db->prepare('
            SELECT l.*, p.title AS product_title, u.name AS user_name, u.email AS user_email
            FROM licenses l
            INNER JOIN purchases pur ON pur.id = l.purchase_id
            INNER JOIN posts p ON p.id = pur.post_id
            INNER JOIN users u ON u.id = pur.user_id
            ORDER BY l.created_at DESC
        ');
        $stmt->execute();
        $licenses = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->adminView('admin/memberships/licenses', array(
            'licenses' => $licenses,
            'currentUser' => $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user')),
        ), array(
            'title' => 'Inspect Licenses',
            'robots' => 'noindex, nofollow',
        ));
    }
}
