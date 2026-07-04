<?php

declare(strict_types=1);

namespace App\Core;

final class Application
{
    private static ?self $instance = null;
    private string $basePath;
    private Config $config;
    private Request $request;
    private Response $response;
    private Router $router;
    private ?Logger $logger = null;
    private ?Session $session = null;
    private ?Database $database = null;
    private ?BaseView $view = null;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '\\/');
        self::$instance = $this;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            throw new \RuntimeException('Application has not been booted yet.');
        }

        return self::$instance;
    }

    public function boot(): void
    {
        Environment::load($this->basePath);

        $this->config = new Config($this->basePath . '/config');
        date_default_timezone_set((string) $this->config->get('app.timezone', 'UTC'));

        $this->request = Request::fromGlobals();
        $this->response = new Response();

        $this->logger = new Logger(
            (string) $this->config->get('logging.path', $this->basePath . '/logs'),
            (string) $this->config->get('logging.channel', 'single')
        );

        $this->session = new Session($this->config->load('session.php'));
        $this->session->start();

        $databaseConfig = $this->config->load('database.php');
        $defaultConnection = (string) ($databaseConfig['default'] ?? 'mysql');
        $connectionConfig = (array) ($databaseConfig['connections'][$defaultConnection] ?? []);
        $this->database = new Database($connectionConfig);
        $this->view = new BaseView($this->basePath . '/app/Views', $this->config);

        (new ErrorHandler(
            (bool) $this->config->get('app.debug', false),
            $this->logger,
            $this->view
        ))->register();

        $this->router = new Router($this);
        foreach ((array) $this->config->get('app.middleware', []) as $alias => $middlewareClass) {
            $this->router->addMiddlewareAlias((string) $alias, (string) $middlewareClass);
        }

        foreach ((array) $this->config->get('app.routes', []) as $routeFile) {
            if (!is_file($routeFile)) {
                continue;
            }

            $register = require $routeFile;

            if (is_callable($register)) {
                $register($this->router);
            }
        }
    }

    public function run(): string
    {
        return $this->router->dispatch($this->request);
    }

    public function basePath(): string
    {
        return $this->basePath;
    }

    public function config(): Config
    {
        return $this->config;
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function response(): Response
    {
        return $this->response;
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function logger(): Logger
    {
        return $this->logger ?? throw new \RuntimeException('Logger not initialized.');
    }

    public function session(): Session
    {
        return $this->session ?? throw new \RuntimeException('Session not initialized.');
    }

    public function database(): Database
    {
        return $this->database ?? throw new \RuntimeException('Database not initialized.');
    }

    public function view(): BaseView
    {
        return $this->view ?? throw new \RuntimeException('View not initialized.');
    }
}
