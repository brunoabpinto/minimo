<?php

namespace Minimo\Core\Console\Commands;

final class ServeCommand
{
    public function __construct(private string $basePath) {}

    public function handle(array $args): int
    {
        if (count($args) > 1) {
            fwrite(STDERR, "Usage: minimo dev [port]" . PHP_EOL);

            return 1;
        }

        $port = $args[0] ?? '8080';

        if (!preg_match('/^\d+$/', $port)) {
            fwrite(STDERR, "Invalid port: {$port}. Port must be a number." . PHP_EOL);

            return 1;
        }

        $portNumber = (int) $port;

        if ($portNumber < 1 || $portNumber > 65535) {
            fwrite(STDERR, "Invalid port: {$port}. Port must be between 1 and 65535." . PHP_EOL);

            return 1;
        }

        $publicPath = $this->basePath . DIRECTORY_SEPARATOR . 'public';

        if (!is_dir($publicPath)) {
            fwrite(STDERR, "Could not find public directory at {$publicPath}" . PHP_EOL);

            return 1;
        }

        $php = escapeshellarg(PHP_BINARY);
        $host = '127.0.0.1';
        $target = "{$host}:{$portNumber}";
        $docroot = escapeshellarg($publicPath);
        $command = "{$php} -S {$target} -t {$docroot}";

        echo "Minimo dev server running at http://{$target}" . PHP_EOL;
        echo "Press Ctrl+C to stop." . PHP_EOL . PHP_EOL;

        passthru($command, $exitCode);

        return $exitCode;
    }
}
