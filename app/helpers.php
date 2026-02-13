<?php

function config(?string $key = null, $default = null)
{
    static $config = null;

    if ($config === null) {
        $configPath = __DIR__ . '/config.php';
        $loaded = is_file($configPath) ? require $configPath : [];
        $config = is_array($loaded) ? $loaded : [];
    }

    if ($key === null) {
        return $config;
    }

    return $config[$key] ?? $default;
}

function render(string $view, ?array $data = []): Minimo\Core\View\ViewResponse
{
    return new Minimo\Core\View\ViewResponse($view, $data);
}

function dd($data): void
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    exit;
}
