<?php

namespace App\Plugins;

use App\Core\PluginInterface;

class VueViewPlugin implements PluginInterface
{
    private const VUE_CDN = 'https://unpkg.com/vue@3/dist/vue.global.prod.js';

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

        $pageKey = $pageKeys[0];
        $viewPath = $this->resolvePagePath($pageKeys, '.vue');
        if ($viewPath === null) {
            return null;
        }

        $source = file_get_contents($viewPath);
        $template = $this->extractFirstBlock($source, 'template') ?? '<div>Empty Vue template.</div>';
        $script = $this->extractScript($source);
        $styles = $this->extractStyles($source);

        $scriptSetupWarning = '';
        if (preg_match('~<script[^>]*\bsetup\b[^>]*>~i', $source)) {
            $scriptSetupWarning = 'This runtime loader does not support <script setup>. Use <script> with export default.';
        }

        return render_with_plugins([
            'type' => 'render_view',
            'view' => 'vue.layout',
            'data' => [
                'title' => ucfirst(basename($pageKey)),
                'template_json' => $this->jsonForInlineScript($template),
                'script_json' => $this->jsonForInlineScript($script),
                'styles' => $styles,
                'initial_props_json' => $this->jsonForInlineScript([
                    'route' => $pageKey,
                    'query' => is_array($context['query'] ?? null) ? $context['query'] : [],
                    'post' => is_array($context['post'] ?? null) ? $context['post'] : [],
                    'body' => is_array($context['body'] ?? null) ? $context['body'] : [],
                ]),
                'vue_cdn' => self::VUE_CDN,
                'script_setup_warning' => $scriptSetupWarning,
            ],
        ]);
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

    private function extractFirstBlock(string $source, string $tag): ?string
    {
        if (!preg_match('~<' . $tag . '(?:\s[^>]*)?>(.*?)</' . $tag . '>~is', $source, $matches)) {
            return null;
        }

        return trim($matches[1]);
    }

    private function extractScript(string $source): string
    {
        if (!preg_match('~<script(?![^>]*\bsetup\b)(?:\s[^>]*)?>(.*?)</script>~is', $source, $matches)) {
            return '';
        }

        return trim($matches[1]);
    }

    private function extractStyles(string $source): string
    {
        if (!preg_match_all('~<style(?:\s[^>]*)?>(.*?)</style>~is', $source, $matches)) {
            return '';
        }

        $styles = array_map(static fn($style) => trim($style), $matches[1]);
        $styles = array_filter($styles, static fn($style) => $style !== '');

        return implode("\n\n", $styles);
    }

    private function jsonForInlineScript(mixed $value): string
    {
        return json_encode($value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
