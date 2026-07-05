<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\UserRepository;
use App\Repositories\MembershipRepository;
use App\Repositories\EngagementRepository;
use App\Repositories\PostRepository;

final class MemberController extends BaseMemberController
{
    private UserRepository $userRepository;
    private MembershipRepository $membershipRepository;
    private EngagementRepository $engagementRepository;
    private PostRepository $postRepository;
    private \App\Repositories\CommerceRepository $commerceRepository;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $db = $this->app->database()->connection();
        $this->userRepository = new UserRepository($db);
        $this->membershipRepository = new MembershipRepository($db);
        $this->engagementRepository = new EngagementRepository($db);
        $this->postRepository = new PostRepository($db);
        $this->commerceRepository = new \App\Repositories\CommerceRepository($db);
    }

    public function dashboard(Request $request)
    {
        $currentUser = $this->currentUser();
        $userId = (int) $currentUser['id'];
        $db = $this->app->database()->connection();

        $membership = $this->membershipRepository->getActiveMembership($userId);
        $identity = $this->interactionIdentity();

        $stmtReads = $db->prepare('SELECT COALESCE(SUM(view_count), 0) FROM reading_history WHERE user_id = :uid');
        $stmtReads->execute(array('uid' => $userId));
        $readCount = (int) $stmtReads->fetchColumn();

        $stmtBm = $db->prepare("SELECT COUNT(*) FROM content_interactions WHERE user_id = :uid AND interaction_type = 'bookmark'");
        $stmtBm->execute(array('uid' => $userId));
        $bookmarkCount = (int) $stmtBm->fetchColumn();

        $stmtLk = $db->prepare("SELECT COUNT(*) FROM content_interactions WHERE user_id = :uid AND interaction_type = 'like'");
        $stmtLk->execute(array('uid' => $userId));
        $likeCount = (int) $stmtLk->fetchColumn();

        $stmtCm = $db->prepare('SELECT COUNT(*) FROM comments WHERE user_id = :uid AND deleted_at IS NULL');
        $stmtCm->execute(array('uid' => $userId));
        $commentCount = (int) $stmtCm->fetchColumn();

        $history = $this->engagementRepository->recentHistory($identity, 5);
        $unreadCount = $this->engagementRepository->unreadNotificationCount($userId);

        $profileFields = array('name', 'username', 'email', 'bio', 'avatar', 'website', 'github', 'linkedin', 'twitter');
        $filledFields = 0;
        foreach ($profileFields as $field) {
            if (!empty($currentUser[$field])) {
                $filledFields++;
            }
        }
        $profileCompletion = (int) round(($filledFields / count($profileFields)) * 100);

        return $this->memberView('member/dashboard', array(
            'currentUser' => $currentUser,
            'membership' => $membership,
            'readCount' => $readCount,
            'bookmarkCount' => $bookmarkCount,
            'likeCount' => $likeCount,
            'commentCount' => $commentCount,
            'history' => $history,
            'unreadCount' => $unreadCount,
            'profileCompletion' => $profileCompletion,
        ), array(
            'title' => 'Member Dashboard',
            'robots' => 'noindex, nofollow',
        ));
    }

    public function profile(Request $request)
    {
        return $this->memberView('member/profile', array(
            'currentUser' => $this->currentUser(),
        ), array(
            'title' => 'My Profile',
            'robots' => 'noindex, nofollow',
        ));
    }

    public function settings(Request $request)
    {
        return $this->memberView('member/settings', array(
            'currentUser' => $this->currentUser(),
        ), array(
            'title' => 'Account Settings',
            'robots' => 'noindex, nofollow',
        ));
    }

    public function security(Request $request)
    {
        return $this->memberView('member/security', array(
            'currentUser' => $this->currentUser(),
        ), array(
            'title' => 'Security Settings',
            'robots' => 'noindex, nofollow',
        ));
    }

    public function bookmarks(Request $request)
    {
        $identity = $this->interactionIdentity();
        $bookmarks = $this->engagementRepository->bookmarkedPostsForUser($identity, 20);

        return $this->memberView('member/bookmarks', array(
            'bookmarks' => $bookmarks,
        ), array(
            'title' => 'My Bookmarks',
            'robots' => 'noindex, nofollow',
        ));
    }

    public function collections(Request $request)
    {
        return $this->memberView('member/collections', array(), array(
            'title' => 'My Collections',
            'robots' => 'noindex, nofollow',
        ));
    }

    public function downloads(Request $request)
    {
        $currentUser = $this->currentUser();
        $userId = (int) $currentUser['id'];
        $db = $this->app->database()->connection();

        // Get list of premium files or products user downloaded
        $statement = $db->prepare('
            SELECT ae.*, p.title, p.slug
            FROM activity_events ae
            LEFT JOIN posts p ON p.id = ae.post_id
            WHERE ae.user_id = :user_id AND ae.event_type = "download"
            ORDER BY ae.created_at DESC
        ');
        $statement->execute(array('user_id' => $userId));
        $downloads = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $this->memberView('member/downloads', array(
            'downloads' => $downloads,
        ), array(
            'title' => 'Download History',
            'robots' => 'noindex, nofollow',
        ));
    }

    public function history(Request $request)
    {
        $identity = $this->interactionIdentity();
        $history = $this->engagementRepository->recentHistory($identity, 20);

        return $this->memberView('member/history', array(
            'history' => $history,
        ), array(
            'title' => 'Reading History',
            'robots' => 'noindex, nofollow',
        ));
    }

    public function notifications(Request $request)
    {
        $currentUser = $this->currentUser();
        $userId = (int) $currentUser['id'];
        $notifications = $this->engagementRepository->notificationsForUser($userId, 20);
        $unreadCount = $this->engagementRepository->unreadNotificationCount($userId);

        return $this->memberView('member/notifications', array(
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ), array(
            'title' => 'Notifications',
            'robots' => 'noindex, nofollow',
        ));
    }

    public function membership(Request $request)
    {
        $currentUser = $this->currentUser();
        $userId = (int) $currentUser['id'];
        $membership = $this->membershipRepository->getActiveMembership($userId);
        $plans = $this->membershipRepository->allPlans();

        return $this->memberView('member/membership', array(
            'membership' => $membership,
            'plans' => $plans,
        ), array(
            'title' => 'Membership Details',
            'robots' => 'noindex, nofollow',
        ));
    }

    public function pricing(Request $request)
    {
        $plans = $this->membershipRepository->allPlans();
        $currentUser = $this->currentUser();
        $activeMembership = $currentUser ? $this->membershipRepository->getActiveMembership((int) $currentUser['id']) : null;

        return $this->memberView('member/pricing', array(
            'plans' => $plans,
            'activeMembership' => $activeMembership,
        ), array(
            'title' => 'Upgrade Plans',
            'robots' => 'noindex, nofollow',
        ));
    }

    public function support(Request $request)
    {
        return $this->memberView('member/support', array(), array(
            'title' => 'Help & Support',
            'robots' => 'noindex, nofollow',
        ));
    }

    public function activity(Request $request)
    {
        $currentUser = $this->currentUser();
        $userId = (int) $currentUser['id'];
        $activity = $this->engagementRepository->activityByUser($userId, 30);

        return $this->memberView('member/activity', array(
            'activity' => $activity,
        ), array(
            'title' => 'My Activity Feed',
            'robots' => 'noindex, nofollow',
        ));
    }

    public function updateProfile(Request $request)
    {
        $currentUser = $this->currentUser();
        if (!$currentUser) {
            return Response::redirect('/login');
        }

        $userId = (int) $currentUser['id'];
        $db = $this->app->database()->connection();

        $name = trim((string) $request->input('name', ''));
        $bio = trim((string) $request->input('bio', ''));
        $website = trim((string) $request->input('website', ''));
        $github = trim((string) $request->input('github', ''));
        $twitter = trim((string) $request->input('twitter', ''));
        $linkedin = trim((string) $request->input('linkedin', ''));
        $avatar = trim((string) $request->input('avatar', ''));

        if ($name === '') {
            $this->app->session()->flash('error', 'Name is required.');
            return Response::redirect('/account/profile');
        }

        $stmt = $db->prepare('UPDATE users SET name = :name, bio = :bio, website = :website, github = :github, twitter = :twitter, linkedin = :linkedin, avatar = :avatar, updated_at = :updated_at WHERE id = :id');
        $stmt->execute(array(
            'name' => substr($name, 0, 120),
            'bio' => substr($bio, 0, 500),
            'website' => substr($website, 0, 255),
            'github' => substr($github, 0, 255),
            'twitter' => substr($twitter, 0, 255),
            'linkedin' => substr($linkedin, 0, 255),
            'avatar' => substr($avatar, 0, 500),
            'updated_at' => date('Y-m-d H:i:s'),
            'id' => $userId,
        ));

        $refreshed = $this->userRepository->findWithRole($userId);
        if ($refreshed) {
            $this->app->session()->set(
                (string) $this->app->config()->get('auth.session_key', 'auth_user'),
                $refreshed
            );
        }

        $this->app->session()->flash('success', 'Profile updated successfully.');
        return Response::redirect('/account/profile');
    }

    public function markAllNotificationsRead(Request $request)
    {
        $currentUser = $this->currentUser();
        if (!$currentUser) {
            return Response::redirect('/login');
        }

        $this->engagementRepository->markAllNotificationsRead((int) $currentUser['id']);
        $this->app->session()->flash('success', 'All notifications marked as read.');
        return Response::redirect('/account/notifications');
    }

    public function checkout(Request $request)
    {
        $currentUser = $this->currentUser();
        if (!$currentUser) {
            return Response::redirect('/login');
        }

        $userId = (int) $currentUser['id'];
        $planId = (int) $request->input('plan_id', 0);

        if ($planId <= 0) {
            $this->app->session()->flash('error', 'Select a valid plan to upgrade.');
            return Response::redirect('/pricing');
        }

        $this->membershipRepository->assignPlan($userId, $planId, null);

        $this->app->session()->flash('success', 'Thank you! Your premium plan is now active.');
        return Response::redirect('/account/membership');
    }

    public function purchases(Request $request)
    {
        $currentUser = $this->currentUser();
        $userId = (int) $currentUser['id'];

        $purchases = $this->commerceRepository->findPurchasesByUserId($userId);
        $orders = $this->commerceRepository->findOrdersByUserId($userId);

        return $this->memberView('member/purchases', array(
            'purchases' => $purchases,
            'orders' => $orders,
        ), array(
            'title' => 'My Purchase History',
            'robots' => 'noindex, nofollow',
        ));
    }

    public function licenses(Request $request)
    {
        $currentUser = $this->currentUser();
        $userId = (int) $currentUser['id'];

        $licenses = $this->commerceRepository->findLicensesByUserId($userId);

        return $this->memberView('member/licenses', array(
            'licenses' => $licenses,
        ), array(
            'title' => 'My Product Licenses',
            'robots' => 'noindex, nofollow',
        ));
    }

    public function billing(Request $request)
    {
        $currentUser = $this->currentUser();
        $userId = (int) $currentUser['id'];

        $orders = $this->commerceRepository->findOrdersByUserId($userId);
        $activeMembership = $this->membershipRepository->getActiveMembership($userId);

        return $this->memberView('member/billing', array(
            'orders' => $orders,
            'activeMembership' => $activeMembership,
        ), array(
            'title' => 'Billing & Invoices',
            'robots' => 'noindex, nofollow',
        ));
    }
}
