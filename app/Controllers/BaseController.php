<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\Request;
use App\Core\Response;

abstract class BaseController
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    protected function request(): Request
    {
        return $this->app->request();
    }

    protected function view(string $template, array $data = [], array $options = []): Response
    {
        return new Response($this->app->view()->render($template, $data, $options));
    }

    protected function json(array $data, int $statusCode = 200): Response
    {
        return Response::json($data, $statusCode);
    }

    protected function redirect(string $url, int $statusCode = 302): Response
    {
        return Response::redirect($url, $statusCode);
    }

    protected function currentUser(): ?array
    {
        $user = $this->app->session()->get($this->app->config()->get('auth.session_key', 'auth_user'));

        return is_array($user) ? $user : null;
    }

    protected function currentUserId(): int
    {
        $currentUser = $this->currentUser();

        return $currentUser !== null && isset($currentUser['id']) ? (int) $currentUser['id'] : 0;
    }

    protected function interactionIdentity(): array
    {
        $currentUser = $this->currentUser();

        if ($currentUser !== null && isset($currentUser['id'])) {
            $userId = (int) $currentUser['id'];

            return array(
                'actor_type' => 'user',
                'actor_key' => 'user:' . $userId,
                'user_id' => $userId,
                'guest_token' => null,
                'display_name' => isset($currentUser['name']) ? (string) $currentUser['name'] : 'User',
                'email' => isset($currentUser['email']) ? (string) $currentUser['email'] : '',
            );
        }

        $sessionKey = 'interaction_guest_token';

        if (!$this->app->session()->has($sessionKey)) {
            $this->app->session()->set($sessionKey, bin2hex(random_bytes(16)));
        }

        $guestToken = (string) $this->app->session()->get($sessionKey, '');

        return array(
            'actor_type' => 'guest',
            'actor_key' => 'guest:' . $guestToken,
            'user_id' => null,
            'guest_token' => $guestToken,
            'display_name' => 'Guest',
            'email' => '',
        );
    }
}
