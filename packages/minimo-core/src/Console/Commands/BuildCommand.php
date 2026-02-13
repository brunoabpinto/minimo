<?php

namespace Minimo\Core\Console\Commands;

use FilesystemIterator;
use Minimo\Core\View\ViewResponse;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;

final class BuildCommand
{
    public function __construct(private string $basePath) {}

    public function handle(array $args): int
    {
        if (count($args) !== 0) {
            fwrite(STDERR, "Usage: minimo build" . PHP_EOL);

            return 1;
        }

        $pagesPath = $this->basePath . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'pages';

        if (!is_dir($pagesPath)) {
            fwrite(STDERR, "Could not find pages directory at {$pagesPath}" . PHP_EOL);

            return 1;
        }

        $buildPath = $this->basePath . DIRECTORY_SEPARATOR . 'build';
        $this->resetBuildDirectory($buildPath);
        $this->copyPublicAssets($buildPath);
        $routes = $this->collectRoutes($pagesPath);
        sort($routes);

        $built = 0;
        $failed = 0;

        foreach ($routes as $route) {
            try {
                $html = $this->renderRoute($route);

                if ($html === null) {
                    fwrite(STDERR, "Skipped {$route}: no renderable view found." . PHP_EOL);
                    $failed++;

                    continue;
                }

                $relativeOutputPath = $this->outputPathForRoute($route);
                $fullOutputPath = $buildPath . DIRECTORY_SEPARATOR . $relativeOutputPath;
                $outputDir = dirname($fullOutputPath);

                if (!is_dir($outputDir)) {
                    mkdir($outputDir, 0777, true);
                }

                file_put_contents($fullOutputPath, $html);
                echo "Built {$relativeOutputPath}" . PHP_EOL;
                $built++;
            } catch (Throwable $e) {
                fwrite(STDERR, "Failed {$route}: {$e->getMessage()}" . PHP_EOL);
                $failed++;
            }
        }

        echo "Build complete: {$built} file(s) generated in build." . PHP_EOL;

        return $failed === 0 ? 0 : 1;
    }

    private function resetBuildDirectory(string $buildPath): void
    {
        if (is_dir($buildPath)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($buildPath, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($iterator as $item) {
                if ($item->isDir()) {
                    rmdir($item->getPathname());
                } else {
                    unlink($item->getPathname());
                }
            }

            rmdir($buildPath);
        }

        mkdir($buildPath, 0777, true);
    }

    private function collectRoutes(string $pagesPath): array
    {
        $routes = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($pagesPath, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $item) {
            if (!$item->isFile()) {
                continue;
            }

            $relativePath = substr($item->getPathname(), strlen($pagesPath) + 1);
            $route = $this->routeFromRelativePath($relativePath);

            if ($route === null) {
                continue;
            }

            $routes[$route] = true;
        }

        return array_keys($routes);
    }

    private function routeFromRelativePath(string $relativePath): ?string
    {
        $path = str_replace('\\', '/', $relativePath);

        if (!preg_match('/\.(blade\.php|blade\.md|md)$/', $path)) {
            return null;
        }

        $route = preg_replace('/\.(blade\.php|blade\.md|md)$/', '', $path);
        $route = trim((string) $route, '/');
        $route = preg_replace('#/index$#', '', $route);
        $route = trim((string) $route, '/');

        return $route === '' ? 'index' : $route;
    }

    private function renderRoute(string $route): ?string
    {
        return (new ViewResponse($route, $this->controllerData($route)))->first();
    }

    private function controllerData(string $route): array
    {
        $parts = explode('/', trim($route, '/'));
        $base = $parts[0] ?? 'index';
        $method = isset($parts[1]) ? 'show' : 'index';
        $controller = 'App\\Controllers\\' . str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $base))) . 'Controller';

        if (!class_exists($controller) || !method_exists($controller, $method)) {
            return [];
        }

        $arg = $method === 'show' ? ($parts[1] ?? null) : null;
        $response = (new $controller())->{$method}($arg);

        return is_array($response) ? $response : [];
    }

    private function outputPathForRoute(string $route): string
    {
        if ($route === 'index') {
            return 'index.html';
        }

        return $route . DIRECTORY_SEPARATOR . 'index.html';
    }

    private function copyPublicAssets(string $buildPath): void
    {
        foreach (['styles', 'images', 'build'] as $dir) {
            $source = $this->basePath . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $dir;
            $target = $buildPath . DIRECTORY_SEPARATOR . $dir;

            if (!is_dir($source)) {
                continue;
            }

            $this->copyDirectory($source, $target);
        }
    }

    private function copyDirectory(string $source, string $target): void
    {
        if (!is_dir($target)) {
            mkdir($target, 0777, true);
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $relative = substr($item->getPathname(), strlen($source) + 1);
            $destination = $target . DIRECTORY_SEPARATOR . $relative;

            if ($item->isDir()) {
                if (!is_dir($destination)) {
                    mkdir($destination, 0777, true);
                }

                continue;
            }

            copy($item->getPathname(), $destination);
        }
    }

}
