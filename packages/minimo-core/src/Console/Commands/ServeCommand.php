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

        if (!is_file($this->basePath . DIRECTORY_SEPARATOR . 'package.json')) {
            fwrite(STDERR, "Could not find package.json. Run from your project root." . PHP_EOL);

            return 1;
        }

        if (!$this->commandExists('npm')) {
            fwrite(STDERR, "npm is required for minimo dev." . PHP_EOL);

            return 1;
        }

        $host = '127.0.0.1';
        $viteHost = '127.0.0.1';
        $vitePort = 5173;
        $viteUrl = "http://{$viteHost}:{$vitePort}";
        $target = "{$host}:{$portNumber}";
        $phpCommand = sprintf(
            'MINIMO_VITE_DEV_SERVER=%s %s -S %s -t %s',
            escapeshellarg($viteUrl),
            escapeshellarg(PHP_BINARY),
            $target,
            escapeshellarg($publicPath)
        );
        $viteCommand = sprintf('npm run dev -- --host %s --port %d', $viteHost, $vitePort);
        $command = sprintf(
            "cd %s && %s & PHP_PID=$!; trap 'kill \$PHP_PID >/dev/null 2>&1' EXIT INT TERM; %s; EXIT_CODE=$?; kill \$PHP_PID >/dev/null 2>&1; wait \$PHP_PID >/dev/null 2>&1; exit \$EXIT_CODE",
            escapeshellarg($this->basePath),
            $phpCommand,
            $viteCommand
        );

        echo "Minimo running at http://{$target}" . PHP_EOL;
        echo "Assets and live reload enabled." . PHP_EOL;
        echo "Press Ctrl+C to stop." . PHP_EOL . PHP_EOL;

        passthru($command, $exitCode);

        return $exitCode;
    }

    private function commandExists(string $command): bool
    {
        $result = shell_exec('command -v ' . escapeshellarg($command) . ' 2>/dev/null');

        return is_string($result) && trim($result) !== '';
    }
}
