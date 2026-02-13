<?php

namespace Minimo\Core\Console\Commands;

final class CreatePageCommand
{
    public function __construct(private string $basePath) {}

    public function handle(array $args): int
    {
        if (count($args) !== 1) {
            fwrite(STDERR, "Usage: minimo create:page <name>" . PHP_EOL);

            return 1;
        }

        $name = $this->normalizeName($args[0]);

        if ($name === '') {
            fwrite(STDERR, "Missing page name. Usage: minimo create:page <name>" . PHP_EOL);

            return 1;
        }

        if (!preg_match('/^[a-z0-9][a-z0-9\\/-]*$/', $name) || str_contains($name, '..')) {
            fwrite(STDERR, "Invalid page name. Use lowercase letters, numbers, dashes, and optional path segments." . PHP_EOL);

            return 1;
        }

        $relativePath = "views/pages/{$name}.blade.php";
        $fullPath = $this->basePath . DIRECTORY_SEPARATOR . $relativePath;
        $directory = dirname($fullPath);

        if (file_exists($fullPath)) {
            fwrite(STDERR, "File already exists: {$relativePath}" . PHP_EOL);

            return 1;
        }

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $title = $this->titleFromName($name);
        file_put_contents($fullPath, $this->template($title));
        echo "Created {$relativePath}" . PHP_EOL;

        return 0;
    }

    private function normalizeName(string $name): string
    {
        $name = trim($name);
        $name = str_replace('\\', '/', $name);
        $name = trim($name, '/');

        if (str_ends_with($name, '.blade.php')) {
            $name = substr($name, 0, -10);
        }

        return strtolower($name);
    }

    private function titleFromName(string $name): string
    {
        $base = basename($name);
        $words = str_replace(['-', '_'], ' ', $base);

        return ucwords($words);
    }

    private function template(string $title): string
    {
        return <<<BLADE
@extends('layouts.app')

@section('content')
    <section class="container">
        <h1>{$title}</h1>
    </section>
@endsection

BLADE;
    }
}
