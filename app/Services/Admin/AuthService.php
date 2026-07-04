<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Core\Application;
use App\Repositories\AuditLogRepository;
use App\Repositories\PasswordResetRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\BaseService;

final class AuthService extends BaseService
{
    private Application $app;
    private PasswordResetRepository $passwordResetRepository;
    private AuditLogRepository $auditLogRepository;
    private RoleRepository $roleRepository;

    public function __construct(Application $app, UserRepository $userRepository, PasswordResetRepository $passwordResetRepository, AuditLogRepository $auditLogRepository, RoleRepository $roleRepository)
    {
        parent::__construct($userRepository);
        $this->app = $app;
        $this->passwordResetRepository = $passwordResetRepository;
        $this->auditLogRepository = $auditLogRepository;
        $this->roleRepository = $roleRepository;
    }

    public function authenticate(string $email, string $password): array
    {
        $user = $this->repository->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return array('success' => false, 'message' => 'Invalid credentials.');
        }

        if (!(int) $user['is_active']) {
            return array('success' => false, 'message' => 'Your account is inactive.');
        }

        $allowedRoles = (array) $this->app->config()->get('auth.allowed_roles', array('admin', 'editor', 'author'));
        $roleSlug = isset($user['role_slug']) ? $user['role_slug'] : 'visitor';

        if (!in_array($roleSlug, $allowedRoles, true)) {
            return array('success' => false, 'message' => 'You do not have access to the admin panel.');
        }

        $this->app->session()->regenerate();
        $this->app->session()->set($this->app->config()->get('auth.session_key', 'auth_user'), array(
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $roleSlug,
            'role_name' => isset($user['role_name']) ? $user['role_name'] : ucfirst($roleSlug),
            'avatar' => isset($user['avatar']) ? $user['avatar'] : '',
        ));

        $this->repository->updateLastLogin((int) $user['id']);
        $this->auditLogRepository->createLog((int) $user['id'], 'login', 'User logged into admin panel.', array(), $this->app->request()->ip(), (string) $this->app->request()->header('User-Agent', ''));

        return array('success' => true, 'user' => $user);
    }

    public function logout(): void
    {
        $user = $this->currentUser();

        if ($user) {
            $this->auditLogRepository->createLog((int) $user['id'], 'logout', 'User logged out of admin panel.', array(), $this->app->request()->ip(), (string) $this->app->request()->header('User-Agent', ''));
        }

        $this->app->session()->forget($this->app->config()->get('auth.session_key', 'auth_user'));
        $this->app->session()->regenerate();
    }

    public function requestPasswordReset(string $email): array
    {
        $user = $this->repository->findByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', time() + ((int) $this->app->config()->get('auth.reset_expiry_minutes', 30) * 60));
            $this->passwordResetRepository->createToken((int) $user['id'], $email, hash('sha256', $token), $expiresAt);
            $this->sendPasswordResetEmail($user, $token);
            $this->auditLogRepository->createLog((int) $user['id'], 'password_reset_requested', 'Password reset requested.', array(), $this->app->request()->ip(), (string) $this->app->request()->header('User-Agent', ''));
        }

        return array('success' => true);
    }

    public function resetPassword(string $email, string $token, string $password, string $passwordConfirmation): array
    {
        if ($password === '' || $password !== $passwordConfirmation) {
            return array('success' => false, 'message' => 'Passwords do not match.');
        }

        $record = $this->passwordResetRepository->findValidToken($email, $token);

        if (!$record) {
            return array('success' => false, 'message' => 'Invalid or expired reset link.');
        }

        $user = $this->repository->findByEmail($email);

        if (!$user) {
            return array('success' => false, 'message' => 'User not found.');
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $this->repository->updatePassword((int) $user['id'], $hashedPassword);
        $this->passwordResetRepository->markUsed((int) $record['id']);
        $this->passwordResetRepository->deleteByEmail($email);
        $this->auditLogRepository->createLog((int) $user['id'], 'password_reset_completed', 'Password reset completed.', array(), $this->app->request()->ip(), (string) $this->app->request()->header('User-Agent', ''));

        return array('success' => true);
    }

    public function currentUser()
    {
        return $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user'));
    }

    private function sendPasswordResetEmail(array $user, string $token): void
    {
        $resetUrl = url('/admin/reset-password?email=' . urlencode($user['email']) . '&token=' . urlencode($token));
        $subject = (string) $this->app->config()->get('app.name', 'Developer Ruhban') . ' Password Reset';
        $message = "Hello {$user['name']},\n\n";
        $message .= "We received a request to reset your admin password.\n";
        $message .= "Reset it here: {$resetUrl}\n\n";
        $message .= 'This link expires in ' . (int) $this->app->config()->get('auth.reset_expiry_minutes', 30) . " minutes.\n\n";
        $message .= "If you did not request this reset, ignore this email.\n";

        $headers = array(
            'From: ' . (string) $this->app->config()->get('app.name', 'Developer Ruhban') . ' <no-reply@' . parse_url((string) $this->app->config()->get('app.url', ''), PHP_URL_HOST) . '>',
        );

        @mail($user['email'], $subject, $message, implode("\r\n", $headers));
    }
}
