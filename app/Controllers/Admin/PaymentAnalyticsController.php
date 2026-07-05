<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\HttpException;
use PDO;

final class PaymentAnalyticsController extends BaseAdminController
{
    public function __construct(\App\Core\Application $app)
    {
        parent::__construct($app);
        
        $currentUser = $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user'));
        if (!is_array($currentUser) || ($currentUser['role'] ?? '') !== 'admin') {
            throw new HttpException('Unauthorized. Access restricted to Super Admin.', 403);
        }
    }

    public function index(Request $request)
    {
        $db = $this->app->database()->connection();

        // 1. Calculate revenues
        $stmt = $db->prepare('SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = "paid" AND DATE(created_at) = CURRENT_DATE');
        $stmt->execute();
        $revToday = (int) $stmt->fetchColumn();

        $stmt = $db->prepare('SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = "paid" AND YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)');
        $stmt->execute();
        $revWeek = (int) $stmt->fetchColumn();

        $stmt = $db->prepare('SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = "paid" AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())');
        $stmt->execute();
        $revMonth = (int) $stmt->fetchColumn();

        $stmt = $db->prepare('SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = "paid" AND YEAR(created_at) = YEAR(CURDATE())');
        $stmt->execute();
        $revYear = (int) $stmt->fetchColumn();

        // Split Categories
        $stmt = $db->prepare('SELECT COALESCE(SUM(o.total_amount), 0) FROM orders o INNER JOIN user_memberships um ON um.user_id = o.user_id WHERE o.status = "paid"');
        $stmt->execute();
        $membershipRev = (int) $stmt->fetchColumn();

        $stmt = $db->prepare('SELECT COALESCE(SUM(oi.price_cents), 0) FROM order_items oi INNER JOIN orders o ON o.id = oi.order_id WHERE o.status = "paid"');
        $stmt->execute();
        $productRev = (int) $stmt->fetchColumn();

        // 2. Count states
        $stmt = $db->prepare('SELECT COUNT(*) FROM payment_transactions WHERE status = "pending"');
        $stmt->execute();
        $pendingCount = (int) $stmt->fetchColumn();

        $stmt = $db->prepare('SELECT COUNT(*) FROM payment_transactions WHERE status = "failed"');
        $stmt->execute();
        $failedCount = (int) $stmt->fetchColumn();

        $stmt = $db->prepare('SELECT COUNT(*) FROM payment_transactions WHERE status = "refunded"');
        $stmt->execute();
        $refundedCount = (int) $stmt->fetchColumn();

        // 3. Top Customers list
        $stmt = $db->prepare('
            SELECT u.name, u.email, COALESCE(SUM(o.total_amount), 0) AS spend
            FROM orders o
            INNER JOIN users u ON u.id = o.user_id
            WHERE o.status = "paid"
            GROUP BY o.user_id, u.name, u.email
            ORDER BY spend DESC
            LIMIT 5
        ');
        $stmt->execute();
        $topCustomers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 4. Popular billing memberships
        $stmt = $db->prepare('
            SELECT mp.name AS plan_name, COUNT(*) AS total 
            FROM user_memberships um
            INNER JOIN membership_plans mp ON mp.id = um.plan_id
            GROUP BY um.plan_id, mp.name
            ORDER BY total DESC
        ');
        $stmt->execute();
        $popularMemberships = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 5. Gateway Status indicators
        $stmt = $db->prepare('SELECT gateway_key, is_active FROM payment_settings');
        $stmt->execute();
        $gatewayStatus = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->adminView('admin/memberships/analytics', array(
            'revToday' => $revToday / 100,
            'revWeek' => $revWeek / 100,
            'revMonth' => $revMonth / 100,
            'revYear' => $revYear / 100,
            'membershipRev' => $membershipRev / 100,
            'productRev' => $productRev / 100,
            'pendingCount' => $pendingCount,
            'failedCount' => $failedCount,
            'refundedCount' => $refundedCount,
            'topCustomers' => $topCustomers,
            'popularMemberships' => $popularMemberships,
            'gatewayStatus' => $gatewayStatus,
            'currentUser' => $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user')),
        ), array(
            'title' => 'Payment Analytics Dashboard',
            'robots' => 'noindex, nofollow',
        ));
    }

    public function docs(Request $request)
    {
        return $this->adminView('admin/memberships/docs', array(
            'currentUser' => $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user')),
        ), array(
            'title' => 'Razorpay & Gateway Setup Guides',
            'robots' => 'noindex, nofollow',
        ));
    }
}
