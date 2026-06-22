<?php

declare(strict_types=1);

namespace App\Router;

final class Router
{
    /** @var array<string, array{controller: string, method: string}> */
    private array $routes = [];

    public function get(string $path, string $controller, string $method): void
    {
        $this->routes['GET'][$path] = ['controller' => $controller, 'method' => $method];
    }

    public function post(string $path, string $controller, string $method): void
    {
        $this->routes['POST'][$path] = ['controller' => $controller, 'method' => $method];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rtrim($path, '/') ?: '/';

        $route = $this->routes[$method][$path] ?? null;

        if ($route === null) {
            http_response_code(404);
            echo 'Page non trouvée.';
            return;
        }

        $controllerClass = $route['controller'];
        $action = $route['method'];

        if (!class_exists($controllerClass)) {
            throw new \RuntimeException("Contrôleur introuvable : {$controllerClass}");
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $action)) {
            throw new \RuntimeException("Méthode introuvable : {$action}");
        }

        $controller->$action();
    }
}
