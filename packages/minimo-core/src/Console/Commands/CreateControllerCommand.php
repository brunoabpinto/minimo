<?php

namespace Minimo\Core\Console\Commands;

final class CreateControllerCommand
{
    public function __construct(private string $basePath) {}

    public function handle(array $args): int
    {
        if (count($args) < 1 || count($args) > 2) {
            fwrite(STDERR, "Usage: minimo create:controller <name> [--md|--blade]" . PHP_EOL);

            return 1;
        }

        $name = $this->normalizeName($args[0]);

        if ($name === '') {
            fwrite(STDERR, "Missing controller name. Usage: minimo create:controller <name> [--md|--blade]" . PHP_EOL);

            return 1;
        }

        if (!$this->isValidName($name)) {
            fwrite(STDERR, "Invalid controller name. Use letters and numbers only (example: FooBarController)." . PHP_EOL);

            return 1;
        }

        $viewExtension = null;
        if (isset($args[1])) {
            $viewExtension = $this->normalizeViewExtension($args[1]);

            if ($viewExtension === null) {
                fwrite(STDERR, "Invalid view option: {$args[1]}. Use --md or --blade." . PHP_EOL);

                return 1;
            }
        }

        $relativePath = "app/Controllers/{$name}.php";
        $fullPath = $this->basePath . DIRECTORY_SEPARATOR . $relativePath;
        $directory = dirname($fullPath);

        $route = null;
        $viewRelativePath = null;
        $viewFullPath = null;
        $tempMdRelativePath = null;
        $tempMdFullPath = null;

        if ($viewExtension !== null) {
            $route = $this->routeFromControllerName($name);

            if (!$this->isValidRouteKey($route)) {
                fwrite(STDERR, "Could not infer a valid route path from {$name}." . PHP_EOL);

                return 1;
            }

            $viewRelativePath = $this->viewRelativePathFromRoute($route, $viewExtension);
            $viewFullPath = $this->basePath . DIRECTORY_SEPARATOR . $viewRelativePath;

            if ($viewExtension === 'blade.md') {
                $tempMdRelativePath = "views/pages/{$route}.md";
                $tempMdFullPath = $this->basePath . DIRECTORY_SEPARATOR . $tempMdRelativePath;
            }
        }

        if (file_exists($fullPath)) {
            fwrite(STDERR, "File already exists: {$relativePath}" . PHP_EOL);

            return 1;
        }

        if ($viewFullPath !== null && file_exists($viewFullPath)) {
            fwrite(STDERR, "File already exists: {$viewRelativePath}" . PHP_EOL);

            return 1;
        }

        if ($tempMdFullPath !== null && file_exists($tempMdFullPath)) {
            fwrite(STDERR, "File already exists: {$tempMdRelativePath}" . PHP_EOL);

            return 1;
        }

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($fullPath, $this->template($name));
        echo "Created {$relativePath}" . PHP_EOL;

        if ($viewExtension !== null && $route !== null) {
            return $this->createViewForRoute($route, $viewExtension);
        }

        return 0;
    }

    private function normalizeName(string $name): string
    {
        $name = trim($name);

        if (str_ends_with($name, '.php')) {
            $name = substr($name, 0, -4);
        }

        if ($name !== '' && !str_ends_with($name, 'Controller')) {
            $name .= 'Controller';
        }

        return $name;
    }

    private function isValidName(string $name): bool
    {
        return (bool) preg_match('/^[A-Z][A-Za-z0-9]*Controller$/', $name);
    }

    private function normalizeViewExtension(string $option): ?string
    {
        return match ($option) {
            '--blade' => 'blade.php',
            '--md' => 'blade.md',
            default => null,
        };
    }

    private function routeFromControllerName(string $name): string
    {
        $base = substr($name, 0, -10);
        $parts = preg_split('/(?<=[a-z0-9])(?=[A-Z])/', $base) ?: [$base];

        $first = strtolower($parts[0]);

        if (count($parts) === 1) {
            return $first;
        }

        $rest = strtolower(implode('', array_slice($parts, 1)));

        return $first . '/' . $rest;
    }

    private function isValidRouteKey(string $route): bool
    {
        return (bool) preg_match('/^[a-z0-9][a-z0-9\\/-]*$/', $route) && !str_contains($route, '..');
    }

    private function viewRelativePathFromRoute(string $route, string $viewExtension): string
    {
        if ($viewExtension === 'blade.php') {
            return "views/pages/{$route}.blade.php";
        }

        return "views/pages/{$route}.blade.md";
    }

    private function createViewForRoute(string $route, string $viewExtension): int
    {
        if ($viewExtension === 'blade.php') {
            return (new CreatePageCommand($this->basePath))->handle([$route]);
        }

        ob_start();
        $exitCode = (new CreatePostCommand($this->basePath))->handle([$route]);
        ob_end_clean();

        if ($exitCode !== 0) {
            return $exitCode;
        }

        $sourceRelativePath = "views/pages/{$route}.md";
        $targetRelativePath = "views/pages/{$route}.blade.md";
        $sourceFullPath = $this->basePath . DIRECTORY_SEPARATOR . $sourceRelativePath;
        $targetFullPath = $this->basePath . DIRECTORY_SEPARATOR . $targetRelativePath;

        if (!rename($sourceFullPath, $targetFullPath)) {
            fwrite(STDERR, "Could not create {$targetRelativePath}" . PHP_EOL);

            return 1;
        }

        echo "Created {$targetRelativePath}" . PHP_EOL;

        return 0;
    }

    private function template(string $className): string
    {
        return <<<PHP
<?php

namespace App\Controllers;

class {$className}
{

    public function index()
    {
        //
    }
}

PHP;
    }
}
