<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];
    private array $groupStack = [];
    private array $middlewareAliases = [];
    private Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function addMiddlewareAlias(string $alias, string $class): void
    {
        $this->middlewareAliases[$alias] = $class;
    }

    public function get(string $uri, $action, array $middleware = []): self
    {
        return $this->addRoute('GET', $uri, $action, $middleware);
    }

    public function post(string $uri, $action, array $middleware = []): self
    {
        return $this->addRoute('POST', $uri, $action, $middleware);
    }

    public function put(string $uri, $action, array $middleware = []): self
    {
        return $this->addRoute('PUT', $uri, $action, $middleware);
    }

    public function patch(string $uri, $action, array $middleware = []): self
    {
        return $this->addRoute('PATCH', $uri, $action, $middleware);
    }

    public function delete(string $uri, $action, array $middleware = []): self
    {
        return $this->addRoute('DELETE', $uri, $action, $middleware);
    }

    public function group(array $attributes, callable $callback): void
    {
        $this->groupStack[] = $attributes;
        $callback($this);
        array_pop($this->groupStack);
    }

    public function dispatch(Request $request): string
    {
        $route = $this->match($request->method(), $request->path());

        if ($route === null) {
            throw new HttpException('Route not found.', 404);
        }

        $middlewareStack = $this->resolveMiddleware($route['middleware'] ?? []);
        $handler = function () use ($route, $request) {
            return $this->executeAction($route['action'], $request, $route['params'] ?? []);
        };

        $pipeline = array_reduce(
            array_reverse($middlewareStack),
            static function (callable $next, string $middlewareClass) use ($request) {
                return function () use ($middlewareClass, $next, $request) {
                    $instance = new $middlewareClass(app());

                    return $instance->handle($request, $next);
                };
            },
            $handler
        );

        $result = $pipeline();

        if ($result instanceof Response) {
            return $result->send();
        }

        if (is_string($result)) {
            return $result;
        }

        if (is_array($result)) {
            return (string) json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return '';
    }

    private function addRoute(string $method, string $uri, $action, array $middleware = []): self
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'uri' => $this->prefix($uri),
            'action' => $action,
            'middleware' => $this->mergeMiddleware($middleware),
        ];

        return $this;
    }

    private function prefix(string $uri): string
    {
        $prefix = '';

        foreach ($this->groupStack as $group) {
            $groupPrefix = trim((string) ($group['prefix'] ?? ''), '/');
            if ($groupPrefix !== '') {
                $prefix .= '/' . $groupPrefix;
            }
        }

        $uri = '/' . trim($uri, '/');

        return '/' . trim($prefix . $uri, '/');
    }

    private function mergeMiddleware(array $middleware): array
    {
        foreach ($this->groupStack as $group) {
            $middleware = array_merge((array) ($group['middleware'] ?? []), $middleware);
        }

        return array_values(array_unique($middleware));
    }

    private function match(string $method, string $path): ?array
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if ($route['uri'] === '/' && $path === '/') {
                return $route;
            }

            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route['uri']);
            $pattern = '#^' . rtrim((string) $pattern, '/') . '$#';

            if (preg_match($pattern, $path, $matches) === 1) {
                $params = array_filter($matches, static fn ($value, $key) => is_string($key), ARRAY_FILTER_USE_BOTH);
                $route['params'] = $params;

                return $route;
            }
        }

        return null;
    }

    private function resolveMiddleware(array $middleware): array
    {
        return array_values(array_filter(array_map(function (string $item): ?string {
            return $this->middlewareAliases[$item] ?? (class_exists($item) ? $item : null);
        }, $middleware)));
    }

    private function executeAction($action, Request $request, array $params)
    {
        if (is_callable($action)) {
            return $action($request, ...$this->normalizeArguments($action, array_values($params)));
        }

        [$controllerClass, $method] = $action;
        $controller = new $controllerClass($this->app);

        return $controller->{$method}($request, ...$this->normalizeArguments(array($controller, $method), array_values($params)));
    }

    private function normalizeArguments($action, array $arguments): array
    {
        try {
            $reflection = is_array($action) ? new \ReflectionMethod($action[0], $action[1]) : new \ReflectionFunction(\Closure::fromCallable($action));
        } catch (\Throwable $exception) {
            return $arguments;
        }

        $normalized = array();
        $parameters = $reflection->getParameters();

        foreach ($arguments as $index => $argument) {
            $parameter = $parameters[$index + 1] ?? null;

            $type = $parameter->getType();

            if ($parameter === null || $type === null || !$type instanceof \ReflectionNamedType) {
                $normalized[] = $argument;
                continue;
            }

            if ($type->isBuiltin() && $type->getName() === 'int') {
                $normalized[] = (int) $argument;
                continue;
            }

            if ($type->isBuiltin() && $type->getName() === 'float') {
                $normalized[] = (float) $argument;
                continue;
            }

            if ($type->isBuiltin() && $type->getName() === 'bool') {
                $normalized[] = filter_var($argument, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
                continue;
            }

            $normalized[] = $argument;
        }

        return $normalized;
    }
}
