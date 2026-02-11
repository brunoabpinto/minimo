<?php

use App\Core\PluginManager;
use App\Core\ViewResponse;

function project_class_basename(string $class): string
{
    return basename(str_replace('\\', '/', $class));
}

function plugin_manager(): PluginManager
{
    static $manager = null;

    if ($manager === null) {
        $manager = PluginManager::fromConfig(__DIR__ . '/plugins.php');
    }

    return $manager;
}

function render_with_plugins(array $context): ?string
{
    return plugin_manager()->handle($context);
}

function view_page_name(): string
{
    $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['class'] ?? '';
    $name = strtolower(str_replace('Controller', '', project_class_basename($caller)));
    return $name !== '' ? $name : 'index';
}

function view(?array $data = null)
{
    if (is_array($data)) {
        render_page_view(view_page_name(), $data);
        return;
    }

    return new ViewResponse(view_page_name());
}

function render_page_view(string $pageName, array $data = []): void
{
    (new ViewResponse($pageName))->render($data);
}
