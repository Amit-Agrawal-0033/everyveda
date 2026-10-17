<?php
/**
 * EveryVeda High Performance Fast HTTP Router
 */

declare(strict_types=1);

namespace App\Core;

class Router
{
    private static array $routes = [];
    private static array $groupStack = [];

    public static function get(string $path, array|callable $handler, array $middleware = []): void
    {
        self::addRoute('GET', $path, $handler, $middleware);
    }

    public static function post(string $path, array|callable $handler, array $middleware = []): void
    {
        self::addRoute('POST', $path, $handler, $middleware);
    }

    public static function put(string $path, array|callable $handler, array $middleware = []): void
    {
        self::addRoute('PUT', $path, $handler, $middleware);
    }

    public static function delete(string $path, array|callable $handler, array $middleware = []): void
    {
        self::addRoute('DELETE', $path, $handler, $middleware);
    }

    public static function group(array $attributes, callable $callback): void
    {
        self::$groupStack[] = $attributes;
        $callback();
        array_pop(self::$groupStack);
    }

    private static function addRoute(string $method, string $path, array|callable $handler, array $middleware = []): void
    {
        $prefix = '';
        $groupMiddleware = [];

        foreach (self::$groupStack as $group) {
            if (isset($group['prefix'])) {
                $prefix .= '/' . trim($group['prefix'], '/');
            }
            if (isset($group['middleware'])) {
                $m = is_array($group['middleware']) ? $group['middleware'] : [$group['middleware']];
                $groupMiddleware = array_merge($groupMiddleware, $m);
            }
        }

        $fullPath = '/' . trim($prefix . '/' . trim($path, '/'), '/');
        if ($fullPath === '//') {
            $fullPath = '/';
        }

        $allMiddleware = array_merge($groupMiddleware, $middleware);

        // Convert route parameters like {id} or {slug} into regex
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $fullPath);
        $regex = '#^' . $pattern . '$#';

        self::$routes[] = [
            'method' => $method,
            'path' => $fullPath,
            'regex' => $regex,
            'handler' => $handler,
            'middleware' => $allMiddleware
        ];
    }

    public static function dispatch(Request $request): void
    {
        $method = $request->method();
        $uri = $request->uri();

        $matchedRoute = null;
        $routeParams = [];

        foreach (self::$routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['regex'], $uri, $matches)) {
                $matchedRoute = $route;
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $routeParams[$key] = urldecode($value);
                    }
                }
                break;
            }
        }

        if (!$matchedRoute) {
            // Check for method not allowed (405)
            $allowedMethods = [];
            foreach (self::$routes as $route) {
                if (preg_match($route['regex'], $uri)) {
                    $allowedMethods[] = $route['method'];
                }
            }

            if (!empty($allowedMethods)) {
                http_response_code(405);
                header('Allow: ' . implode(', ', array_unique($allowedMethods)));
                echo "405 Method Not Allowed";
                return;
            }

            // 404 Not Found
            http_response_code(404);
            View::render('pages/404', [
                'meta_title' => __('404_not_found')
            ]);
            return;
        }

        // Execute Middleware Pipeline
        foreach ($matchedRoute['middleware'] as $middlewareClass) {
            $middleware = new $middlewareClass();
            $handled = $middleware->handle($request);
            if ($handled === false) {
                return; // Middleware handled the response (e.g. redirected or unauthorized)
            }
        }

        // Invoke Controller / Handler
        $handler = $matchedRoute['handler'];

        if (is_callable($handler)) {
            call_user_func_array($handler, array_merge([$request], $routeParams));
            return;
        }

        if (is_array($handler) && count($handler) === 2) {
            [$class, $action] = $handler;
            if (!class_exists($class)) {
                throw new \RuntimeException("Controller class {$class} not found.");
            }
            $controller = new $class();
            if (!method_exists($controller, $action)) {
                throw new \RuntimeException("Method {$action} not found on controller {$class}.");
            }

            call_user_func_array([$controller, $action], array_merge([$request], $routeParams));
            return;
        }

        throw new \RuntimeException("Invalid route handler defined for {$uri}.");
    }
}
