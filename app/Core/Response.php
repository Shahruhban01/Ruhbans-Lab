<?php

declare(strict_types=1);

namespace App\Core;

final class Response
{
    private string $content = '';
    private int $statusCode = 200;
    private array $headers = [];

    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public static function json(array $data, int $statusCode = 200, array $headers = []): self
    {
        $headers['Content-Type'] = 'application/json; charset=utf-8';

        return new self((string) json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $statusCode, $headers);
    }

    public static function redirect(string $url, int $statusCode = 302): self
    {
        return (new self('', $statusCode))->header('Location', $url);
    }

    public function send(): string
    {
        if (!headers_sent()) {
            http_response_code($this->statusCode);

            $securityHeaders = array(
                'X-Content-Type-Options' => 'nosniff',
                'X-Frame-Options' => 'SAMEORIGIN',
                'Referrer-Policy' => 'strict-origin-when-cross-origin',
                'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
                'X-XSS-Protection' => '1; mode=block',
            );

            foreach ($securityHeaders as $name => $value) {
                if (!isset($this->headers[$name])) {
                    header($name . ': ' . $value, true);
                }
            }

            foreach ($this->headers as $name => $value) {
                header($name . ': ' . $value, true);
            }
        }

        return $this->content;
    }
}
