<?php

namespace Minimo\Core\Console\Commands;

final class PreviewCommand
{
    public function __construct(private string $basePath) {}

    public function handle(array $args): int
    {
        if (count($args) > 1) {
            fwrite(STDERR, "Usage: minimo preview [port]" . PHP_EOL);

            return 1;
        }

        $port = $args[0] ?? '9090';

        if (!preg_match('/^\d+$/', $port)) {
            fwrite(STDERR, "Invalid port: {$port}. Port must be a number." . PHP_EOL);

            return 1;
        }

        $portNumber = (int) $port;

        if ($portNumber < 1 || $portNumber > 65535) {
            fwrite(STDERR, "Invalid port: {$port}. Port must be between 1 and 65535." . PHP_EOL);

            return 1;
        }

        $buildExitCode = (new BuildCommand($this->basePath))->handle([]);

        if ($buildExitCode !== 0) {
            return $buildExitCode;
        }

        $buildPath = $this->basePath . DIRECTORY_SEPARATOR . 'build';

        if (!is_dir($buildPath)) {
            fwrite(STDERR, "Could not find build directory at {$buildPath}" . PHP_EOL);

            return 1;
        }

        $host = '127.0.0.1';
        $target = "{$host}:{$portNumber}";
        $command = sprintf(
            '%s -S %s -t %s',
            escapeshellarg(PHP_BINARY),
            $target,
            escapeshellarg($buildPath)
        );

        echo PHP_EOL;
        echo "Minimo preview running at http://{$target}" . PHP_EOL;
        echo "Press Ctrl+C to stop." . PHP_EOL . PHP_EOL;

        passthru($command, $exitCode);

        return $exitCode;
    }
}
