<?php

namespace Minimo\Core\Support;

final class PathResolver
{
    private static ?string $basePath = null;

    public static function basePath(): string
    {
        if (self::$basePath !== null) {
            return self::$basePath;
        }

        $candidates = [];
        $scriptFilename = $_SERVER['SCRIPT_FILENAME'] ?? null;

        if (is_string($scriptFilename) && $scriptFilename !== '') {
            $candidates[] = dirname($scriptFilename);
            $candidates[] = dirname(dirname($scriptFilename));
        }

        $cwd = getcwd();
        if ($cwd !== false) {
            $candidates[] = $cwd;
        }

        // Works when package is loaded from vendor/...
        $candidates[] = dirname(__DIR__, 5);
        // Works when package is loaded from packages/... (path repo symlink)
        $candidates[] = dirname(__DIR__, 4);

        foreach ($candidates as $candidate) {
            $resolved = self::searchUpForProjectRoot($candidate);
            if ($resolved !== null) {
                self::$basePath = $resolved;
                return self::$basePath;
            }
        }

        self::$basePath = dirname(__DIR__, 4);
        return self::$basePath;
    }

    private static function searchUpForProjectRoot(string $start): ?string
    {
        $path = realpath($start) ?: $start;

        while (true) {
            if (self::looksLikeProjectRoot($path)) {
                return $path;
            }

            $parent = dirname($path);
            if ($parent === $path) {
                break;
            }

            $path = $parent;
        }

        return null;
    }

    private static function looksLikeProjectRoot(string $path): bool
    {
        return is_file($path . '/composer.json')
            && is_dir($path . '/views')
            && is_dir($path . '/storage');
    }
}
