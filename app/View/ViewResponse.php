<?php

namespace App\View;

use App\View\BladeRenderer;
use App\View\MarkdownRenderer;

final class ViewResponse
{

    public function __construct(private string $path, private ?array $data = []) {}

    public function first(): ?string
    {
        $files = glob(__DIR__ . "/../../views/pages/{$this->path}.*");

        if (empty($files)) {
            $files = glob(__DIR__ . "/../../views/pages/{$this->path}/index.*");

            if (empty($files)) {
                return null;
            }

            $this->path .= '/index';
        }

        foreach ($files as $file) {
            if (str_ends_with($file, '.blade.php')) {
                $this->path = 'pages.' . str_replace('/', '.', $this->path);
                return $this->blade();
            }
            if (str_ends_with($file, '.md')) {
                return $this->markdown($file);
            }
        }
        return null;
    }

    public function blade(): string
    {
        return (new BladeRenderer())->render($this->path, $this->data);
    }

    public function markdown(string $path): string
    {
        return (new MarkdownRenderer())->render($path);
    }
}
