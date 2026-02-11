<?php

namespace App\Plugins;

use App\Core\PluginInterface;
use Parsedown;

class MarkdownPlugin implements PluginInterface
{
    public function handle(array $context): ?string
    {
        if (($context['type'] ?? '') !== 'resolve_route') {
            return null;
        }

        $route = trim((string) ($context['route'] ?? ''), '/');
        $pageKeys = $this->routeToPageKeys($route);
        if ($pageKeys === null) {
            return null;
        }

        $md = $this->resolvePagePath($pageKeys, '.md');
        if ($md === null) {
            return null;
        }

        $content = $this->parseMarkdown(file_get_contents($md));

        $rendered = render_with_plugins([
            'type' => 'render_view',
            'view' => 'markdown.layout',
            'data' => ['content' => $content],
        ]);

        return $rendered ?? $content;
    }

    private function parseMarkdown(string $markdown): string
    {
        // Parsedown emits PHP 8.4+ deprecations from its internal signatures.
        // Ignore only those notices coming from Parsedown to keep app output clean.
        set_error_handler(static function (int $severity, string $message, string $file): bool {
            if ($severity !== E_DEPRECATED && $severity !== E_USER_DEPRECATED) {
                return false;
            }

            $normalizedFile = str_replace('\\', '/', $file);
            return str_contains($normalizedFile, '/vendor/erusev/parsedown/Parsedown.php');
        });

        try {
            $parsedown = new Parsedown();
            return $parsedown->text($markdown);
        } finally {
            restore_error_handler();
        }
    }

    private function routeToPageKeys(string $route): ?array
    {
        if ($route === '' || str_contains($route, '..')) {
            return null;
        }

        if (!preg_match('~^[a-zA-Z0-9/_-]+$~', $route)) {
            return null;
        }

        $keys = [$route];
        $flatRoute = str_replace('/', '-', $route);
        if ($flatRoute !== $route) {
            $keys[] = $flatRoute;
        }

        return $keys;
    }

    private function resolvePagePath(array $pageKeys, string $extension): ?string
    {
        $pagesPath = __DIR__ . '/../../views/pages';

        foreach ($pageKeys as $pageKey) {
            $candidate = $pagesPath . '/' . $pageKey . $extension;
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
