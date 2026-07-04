<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Repositories\AuditLogRepository;
use App\Repositories\PasswordResetRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Core\Request;
use App\Services\Admin\AuthService;

final class AuthController extends BaseAdminController
{
    private AuthService $authService;

    public function __construct(\App\Core\Application $app)
    {
        parent::__construct($app);
        $connection = $this->app->database()->connection();
        $userRepository = new UserRepository($connection);
        $passwordResetRepository = new PasswordResetRepository($connection);
        $auditLogRepository = new AuditLogRepository($connection);
        $roleRepository = new RoleRepository($connection);

        $this->authService = new AuthService($this->app, $userRepository, $passwordResetRepository, $auditLogRepository, $roleRepository);
    }

    public function showLogin(Request $request)
    {
        return $this->authView('admin/auth/login', array(
            'errors' => array(),
            'old' => array(),
        ), array(
            'title' => 'Admin Login',
            'description' => 'Secure access to the Developer Ruhban admin dashboard.',
            'robots' => 'noindex, nofollow',
        ));
    }

    public function login(Request $request)
    {
        $email = trim((string) $request->input('email', ''));
        $password = (string) $request->input('password', '');
        $errors = array();

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        }

        if ($password === '') {
            $errors['password'] = 'Password is required.';
        }

        if ($errors !== array()) {
            return $this->authView('admin/auth/login', array('errors' => $errors, 'old' => array('email' => $email)), array('title' => 'Admin Login', 'robots' => 'noindex, nofollow'))->setStatusCode(422);
        }

        $result = $this->authService->authenticate($email, $password);

        if (empty($result['success'])) {
            return $this->authView('admin/auth/login', array('errors' => array('general' => $result['message']), 'old' => array('email' => $email)), array('title' => 'Admin Login', 'robots' => 'noindex, nofollow'))->setStatusCode(422);
        }

        $this->app->session()->flash('success', 'Welcome back.');

        return $this->redirect((string) $this->app->config()->get('auth.dashboard_redirect', '/admin'));
    }

    public function logout(Request $request)
    {
        $this->authService->logout();
        $this->app->session()->flash('success', 'You have been signed out.');

        return $this->redirect((string) $this->app->config()->get('auth.guest_redirect', '/admin/login'));
    }

    public function showForgotPassword(Request $request)
    {
        return $this->authView('admin/auth/forgot-password', array('errors' => array(), 'old' => array()), array('title' => 'Forgot Password', 'robots' => 'noindex, nofollow'));
    }

    public function sendPasswordResetLink(Request $request)
    {
        $email = trim((string) $request->input('email', ''));
        $errors = array();

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        }

        if ($errors !== array()) {
            return $this->authView('admin/auth/forgot-password', array('errors' => $errors, 'old' => array('email' => $email)), array('title' => 'Forgot Password', 'robots' => 'noindex, nofollow'))->setStatusCode(422);
        }

        $this->authService->requestPasswordReset($email);
        $this->app->session()->flash('success', 'If the account exists, a reset link has been sent.');

        return $this->redirect('/admin/forgot-password');
    }

    public function showResetPassword(Request $request)
    {
        $email = trim((string) $request->input('email', ''));
        $token = trim((string) $request->input('token', ''));

        if ($email === '' || $token === '') {
            $this->app->session()->flash('error', 'Invalid reset link.');

            return $this->redirect('/admin/forgot-password');
        }

        return $this->authView('admin/auth/reset-password', array(
            'errors' => array(),
            'old' => array('email' => $email, 'token' => $token),
        ), array('title' => 'Reset Password', 'robots' => 'noindex, nofollow'));
    }

    public function resetPassword(Request $request)
    {
        $email = trim((string) $request->input('email', ''));
        $token = trim((string) $request->input('token', ''));
        $password = (string) $request->input('password', '');
        $passwordConfirmation = (string) $request->input('password_confirmation', '');
        $errors = array();

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        }

        if ($token === '') {
            $errors['token'] = 'Reset token is required.';
        }

        if ($password === '') {
            $errors['password'] = 'New password is required.';
        }

        if ($password !== $passwordConfirmation) {
            $errors['password_confirmation'] = 'Passwords must match.';
        }

        if ($errors !== array()) {
            return $this->authView('admin/auth/reset-password', array('errors' => $errors, 'old' => array('email' => $email, 'token' => $token)), array('title' => 'Reset Password', 'robots' => 'noindex, nofollow'))->setStatusCode(422);
        }

        $result = $this->authService->resetPassword($email, $token, $password, $passwordConfirmation);

        if (empty($result['success'])) {
            return $this->authView('admin/auth/reset-password', array('errors' => array('general' => $result['message']), 'old' => array('email' => $email, 'token' => $token)), array('title' => 'Reset Password', 'robots' => 'noindex, nofollow'))->setStatusCode(422);
        }

        $this->app->session()->flash('success', 'Password updated successfully.');

        return $this->redirect('/admin/login');
    }
}
