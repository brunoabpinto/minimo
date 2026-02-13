<?php

namespace Minimo\Core\Support;

final class Vite
{
    public static function assets(array|string $entries, string $basePath): string
    {
        $entries = self::normalizeEntries($entries);

        if ($entries === []) {
            return '';
        }

        $publicPath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'public';
        $hotFile = $publicPath . DIRECTORY_SEPARATOR . 'hot';
        $envDevServer = getenv('MINIMO_VITE_DEV_SERVER');

        if (is_string($envDevServer) && trim($envDevServer) !== '') {
            return self::devTags($entries, trim($envDevServer));
        }

        if (PHP_SAPI !== 'cli' && is_file($hotFile)) {
            $devServer = trim((string) file_get_contents($hotFile));
            if ($devServer === '') {
                $devServer = 'http://127.0.0.1:5173';
            }

            return self::devTags($entries, $devServer);
        }

        $manifest = self::manifest($publicPath . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR . 'manifest.json');

        if ($manifest === []) {
            return '';
        }

        $scripts = [];
        $styles = [];
        $seen = [];

        foreach ($entries as $entry) {
            self::collectManifestAssets($manifest, $entry, $scripts, $styles, $seen);
        }

        if ($scripts === [] && $styles === []) {
            return '';
        }

        $tags = [];

        foreach ($styles as $style) {
            $tags[] = '<link rel="stylesheet" href="/build/' . ltrim($style, '/') . '" />';
        }

        foreach ($scripts as $script) {
            $tags[] = '<script type="module" src="/build/' . ltrim($script, '/') . '"></script>';
        }

        return implode(PHP_EOL, $tags);
    }

    private static function devTags(array $entries, string $devServer): string
    {
        $devServer = rtrim($devServer, '/');
        $tags = ['<script type="module" src="' . $devServer . '/@vite/client"></script>'];

        foreach ($entries as $entry) {
            if (str_ends_with($entry, '.css')) {
                $tags[] = '<link rel="stylesheet" href="' . $devServer . '/' . $entry . '" />';
                continue;
            }

            $tags[] = '<script type="module" src="' . $devServer . '/' . $entry . '"></script>';
        }

        return implode(PHP_EOL, $tags);
    }

    private static function normalizeEntries(array|string $entries): array
    {
        return array_values(array_filter(array_map(
            static fn (string $entry): string => ltrim(trim($entry), '/'),
            (array) $entries
        )));
    }

    private static function manifest(string $manifestPath): array
    {
        if (!is_file($manifestPath)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($manifestPath), true);

        return is_array($decoded) ? $decoded : [];
    }

    private static function collectManifestAssets(array $manifest, string $entry, array &$scripts, array &$styles, array &$seen): void
    {
        if (!isset($manifest[$entry]) || isset($seen[$entry])) {
            return;
        }

        $seen[$entry] = true;
        $chunk = $manifest[$entry];

        if (isset($chunk['file']) && str_ends_with((string) $chunk['file'], '.js')) {
            $scripts[$chunk['file']] = $chunk['file'];
        }

        foreach (($chunk['css'] ?? []) as $css) {
            if (is_string($css) && $css !== '') {
                $styles[$css] = $css;
            }
        }

        foreach (($chunk['imports'] ?? []) as $import) {
            if (!is_string($import) || $import === '') {
                continue;
            }

            self::collectManifestAssets($manifest, $import, $scripts, $styles, $seen);
        }
    }
}
