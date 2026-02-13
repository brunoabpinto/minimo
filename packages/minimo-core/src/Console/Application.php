<?php

namespace Minimo\Core\Console;

use Minimo\Core\Console\Commands\CreatePageCommand;
use Minimo\Core\Console\Commands\CreatePostCommand;
use Minimo\Core\Console\Commands\ServeCommand;

final class Application
{
    public function __construct(private string $basePath) {}

    public function run(array $argv): int
    {
        $command = $argv[1] ?? 'help';

        if (in_array($command, ['help', '--help', '-h'], true)) {
            $this->printHelp();

            return 0;
        }

        if ($command === 'create:post') {
            return (new CreatePostCommand($this->basePath))->handle(array_slice($argv, 2));
        }

        if ($command === 'create:page') {
            return (new CreatePageCommand($this->basePath))->handle(array_slice($argv, 2));
        }

        if ($command === 'dev') {
            return (new ServeCommand($this->basePath))->handle(array_slice($argv, 2));
        }

        fwrite(STDERR, "Unknown command: {$command}" . PHP_EOL . PHP_EOL);
        $this->printHelp();

        return 1;
    }

    private function printHelp(): void
    {
        echo <<<TXT
Minimo CLI

Usage:
  minimo <command> [arguments]

Available commands:
  create:page <name>   Create a Blade page in views
  create:post <slug>   Create a markdown post in views/pages
  dev [port]           Start the local PHP dev server (default: 8080)

TXT;
    }
}
