<?php

namespace App\View;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\MarkdownConverter;

class MarkdownRenderer
{
    private MarkdownConverter $converter;

    public function __construct()
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new FrontMatterExtension());
        $this->converter = new MarkdownConverter($environment);
    }

    public function render(string $path): ?string
    {
        return $this->renderFromSource(file_get_contents($path));
    }

    public function renderString(string $source): ?string
    {
        return $this->renderFromSource($source);
    }

    public function getPost(string $filePath): ?array
    {
        if (empty($filePath)) {
            return null;
        }

        $data = $this->convertSource(file_get_contents($filePath));
        $post = $data['frontMatter'];
        $post['url'] = '/blog/' . pathinfo($filePath, PATHINFO_FILENAME);

        return $post;
    }

    private function renderFromSource(string $source): ?string
    {
        $data = $this->convertSource($source);
        $content = $data['content'];

        $rendered = render(
            'layouts.markdown',
            [
                'content' => $content,
                'frontMatter' => $data['frontMatter'],
            ],
        )->blade();

        return $rendered ?? $content;
    }

    private function convertSource(string $source): array
    {
        $converted = $this->converter->convert($source);

        return [
            'content' => $converted->getContent(),
            'frontMatter' => $this->extractFrontMatter($converted),
        ];
    }

    private function extractFrontMatter(mixed $converted): array
    {
        if (!$converted instanceof RenderedContentWithFrontMatter) {
            return [];
        }

        return is_array($converted->getFrontMatter()) ? $converted->getFrontMatter() : [];
    }
}
