<?php

namespace Minimo\Core;

use Minimo\Core\View\ViewResponse;

final class Kernel
{
    public function run(): void
    {
        $route = $this->requestRoute();
        $parts = $this->requestParts($route);
        $method = $this->resolveMethod($parts);
        $controllerClass = $this->resolveControllerClass($parts);

        $controllerResponse = null;

        if (class_exists($controllerClass) && method_exists($controllerClass, $method)) {
            $arg = $this->resolveControllerArg($method, $parts);
            $controllerResponse = (new $controllerClass())->$method($arg);
        }

        $viewData = is_array($controllerResponse) ? $controllerResponse : [];
        $pageResponse = (new ViewResponse($route, $viewData))->first();

        if ($pageResponse !== null) {
            echo $pageResponse;
            return;
        }

        http_response_code(404);
        echo (new ViewResponse('errors.404'))->blade() ?? 'Not found.';
    }

    private function requestRoute(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $path = parse_url($uri, PHP_URL_PATH);

        return trim((string) $path, '/') ?: 'index';
    }

    private function requestParts(string $route): array
    {
        return explode('/', $route);
    }

    private function resolveMethod(array $parts): string
    {
        $method = match ($_SERVER['REQUEST_METHOD'] ?? 'GET') {
            'POST' => 'create',
            'PUT' => 'update',
            'DELETE' => 'delete',
            default => 'index',
        };

        if (isset($parts[1])) {
            return 'show';
        }

        return $method;
    }

    private function resolveControllerClass(array $parts): string
    {
        $name = ucfirst($parts[0] ?: 'index');

        if (!is_numeric($parts[1] ?? null)) {
            $name .= ucfirst($parts[1] ?? '');
        }

        return 'App\\Controllers\\' . $name . 'Controller';
    }

    private function resolveControllerArg(string $method, array $parts): mixed
    {
        return match ($method) {
            'show' => $parts[1] ?? null,
            'create' => $_POST,
            'update', 'delete' => json_decode(file_get_contents('php://input'), true) ?? [],
            default => null,
        };
    }
}
