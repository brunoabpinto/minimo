<?php

namespace Minimo\Core\View;

use Minimo\Core\Support\PathResolver;

final class ViewResponse
{
    private ?BladeRenderer $bladeRenderer = null;
    private ?MarkdownRenderer $markdownRenderer = null;

    public function __construct(private string $path, private ?array $data = []) {}

    public function first(): ?string
    {
        $resolvedPath = $this->path;
        $files = $this->findPageFiles($resolvedPath);

        if (empty($files)) {
            $resolvedPath .= '/index';
            $files = $this->findPageFiles($resolvedPath);

            if (empty($files)) {
                return null;
            }
        }

        $this->path = $resolvedPath;

        foreach ($files as $file) {
            $rendered = $this->renderResolvedFile($file);

            if ($rendered !== null) {
                return $rendered;
            }
        }

        return null;
    }

    public function blade(): string
    {
        return $this->bladeRenderer()->render($this->path, $this->data);
    }

    public function markdown(string $path): string
    {
        return $this->markdownRenderer()->render($path);
    }

    public function markdownString(string $md): string
    {
        return $this->markdownRenderer()->renderString($md);
    }

    private function findPageFiles(string $routePath): array
    {
        $files = glob($this->pagesPath() . "/{$routePath}.*");

        return $files ?: [];
    }

    private function renderResolvedFile(string $file): ?string
    {
        if (str_ends_with($file, '.blade.md')) {
            return $this->markdownString($this->renderBladePage());
        }

        if (str_ends_with($file, '.blade.php')) {
            return $this->renderBladePage();
        }

        if (str_ends_with($file, '.md')) {
            return $this->markdown($file);
        }

        return null;
    }

    private function bladeRenderer(): BladeRenderer
    {
        if ($this->bladeRenderer !== null) {
            return $this->bladeRenderer;
        }

        $this->bladeRenderer = new BladeRenderer();

        return $this->bladeRenderer;
    }

    private function markdownRenderer(): MarkdownRenderer
    {
        if ($this->markdownRenderer !== null) {
            return $this->markdownRenderer;
        }

        $this->markdownRenderer = new MarkdownRenderer();

        return $this->markdownRenderer;
    }

    private function toBladeViewPath(string $routePath): string
    {
        return 'pages.' . str_replace('/', '.', $routePath);
    }

    private function renderBladePage(): string
    {
        return $this->bladeRenderer()->render($this->toBladeViewPath($this->path), $this->data);
    }

    private function pagesPath(): string
    {
        return PathResolver::basePath() . '/views/pages';
    }
}
