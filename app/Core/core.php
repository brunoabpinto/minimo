<?php

require_once __DIR__ . '/../../vendor/autoload.php';

function request_route(): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $path = parse_url($uri, PHP_URL_PATH);
    return trim((string) $path, '/');
}

function request_parts(string $route): array
{
    return explode('/', $route);
}

function resolve_method(array $parts): string
{
    $method = match ($_SERVER['REQUEST_METHOD'] ?? 'GET') {
        'POST'   => 'create',
        'PUT'    => 'update',
        'DELETE' => 'delete',
        default  => 'index',
    };

    if (isset($parts[1]) && is_numeric($parts[1])) {
        return 'show';
    }

    return $method;
}

function resolve_controller_class(array $parts): string
{
    $name = ucfirst($parts[0] ?: 'index');

    if (!is_numeric($parts[1] ?? null)) {
        $name .= ucfirst($parts[1] ?? '');
    }

    return 'App\\Controllers\\' . $name . 'Controller';
}

function resolve_controller_arg(string $method, array $parts): mixed
{
    return match ($method) {
        'show'   => $parts[1] ?? null,
        'create' => $_POST,
        'update', 'delete' => json_decode(file_get_contents('php://input'), true) ?? [],
        default  => null,
    };
}

function render_plugin_response(array $context): ?string
{
    return render_with_plugins($context);
}

$route = request_route();
$parts = request_parts($route);
$method = resolve_method($parts);
$controllerClass = resolve_controller_class($parts);

if (class_exists($controllerClass) && method_exists($controllerClass, $method)) {
    $arg = resolve_controller_arg($method, $parts);
    $response = (new $controllerClass())->$method($arg);
    if ($response !== null) {
        echo $response;
    }
    exit;
}

$pluginResponse = render_plugin_response([
    'type' => 'resolve_route',
    'route' => $route,
    'parts' => $parts,
    'method' => $method,
    'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
    'query' => $_GET,
    'post' => $_POST,
    'body' => json_decode(file_get_contents('php://input'), true) ?? [],
]);
if ($pluginResponse !== null) {
    echo $pluginResponse;
    exit;
}

http_response_code(404);
echo 'Not found.';
