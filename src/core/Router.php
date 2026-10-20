<?php

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'PATCH' => [],
        'DELETE' => [],
    ];

    public function get(string $uri, $callback): void
    {
        $this->routes['GET'][$this->normalize($uri)] = $callback;
    }

    public function post(string $uri, $callback): void
    {
        $this->routes['POST'][$this->normalize($uri)] = $callback;
    }

    public function put(string $uri, $callback): void
    {
        $this->routes['PUT'][$this->normalize($uri)] = $callback;
    }

    public function patch(string $uri, $callback): void
    {
        $this->routes['PATCH'][$this->normalize($uri)] = $callback;
    }

    public function delete(string $uri, $callback): void
    {
        $this->routes['DELETE'][$this->normalize($uri)] = $callback;
    }

    public function any(string $uri, $callback): void
    {
        foreach (array_keys($this->routes) as $method) {
            $this->routes[$method][$this->normalize($uri)] = $callback;
        }
    }

    public function dispatch(string $uri = null): void
    {
        $uri = $this->normalize($uri ?? ($_SERVER['REQUEST_URI'] ?? '/'));
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        if (isset($this->routes[$method][$uri])) {
            $this->call($this->routes[$method][$uri]);
            return;
        }

        foreach ($this->routes[$method] as $pattern => $callback) {
            if ($this->matchesPattern($pattern, $uri)) {
                $params = $this->extractParams($pattern, $uri);
                $this->call($callback, $params);
                return;
            }
        }

        http_response_code(404);
        echo '404 - Page not found';
        exit;
    }

    protected function call($callback, array $params = []): void
    {
        if (is_callable($callback)) {
            call_user_func_array($callback, $params);
            return;
        }

        if (is_string($callback)) {
            [$controllerName, $method] = array_pad(explode('@', $callback, 2), 2, 'index');
            $controller = new $controllerName();
            call_user_func_array([$controller, $method], $params);
            return;
        }

        if (is_array($callback) && count($callback) === 2) {
            [$controller, $method] = $callback;
            $obj = new $controller();
            call_user_func_array([$obj, $method], $params);
            return;
        }

        throw new RuntimeException('Invalid route callback.');
    }

    protected function normalize(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: $uri;

        $scriptBase = $_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '';
        if ($scriptBase !== '') {
            $scriptBase = rtrim(dirname($scriptBase), '/');
            if ($scriptBase !== '' && $scriptBase !== '.' && strpos($path, $scriptBase) === 0) {
                $path = substr($path, strlen($scriptBase));
            }
        }

        $basePrefixes = [
            '/project-root',
            '/works/project-root',
            '/project-root/public',
            '/works/project-root/public',
            '/public',
        ];

        foreach ($basePrefixes as $base) {
            if ($path === $base) {
                return '/';
            }

            if (strpos($path, $base) === 0) {
                $path = substr($path, strlen($base));
                break;
            }
        }

        $path = '/' . trim($path, '/');
        return $path === '' ? '/' : $path;
    }

    protected function matchesPattern(string $pattern, string $uri): bool
    {
        if ($pattern === $uri) {
            return true;
        }

        $patternParts = explode('/', trim($pattern, '/'));
        $uriParts = explode('/', trim($uri, '/'));

        if (count($patternParts) !== count($uriParts)) {
            return false;
        }

        foreach ($patternParts as $index => $part) {
            if (preg_match('/^\{[a-zA-Z0-9_]+\}$/', $part)) {
                continue;
            }

            if ($part !== ($uriParts[$index] ?? '')) {
                return false;
            }
        }

        return true;
    }

    protected function extractParams(string $pattern, string $uri): array
    {
        $patternParts = explode('/', trim($pattern, '/'));
        $uriParts = explode('/', trim($uri, '/'));
        $params = [];

        foreach ($patternParts as $index => $part) {
            if (preg_match('/^\{([a-zA-Z0-9_]+)\}$/', $part, $matches)) {
                $params[] = $uriParts[$index] ?? null;
            }
        }

        return $params;
    }
}

