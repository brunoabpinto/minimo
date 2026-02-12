<?php

namespace App\View;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
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
        $source = file_get_contents($path);

        $converted = $this->converter->convert($source);
        $frontMatter = $converted->getFrontMatter();
        $content = $converted->getContent();

        $rendered = render(
            'markdown.layout',
            [
                'content' => $content,
                'frontMatter' => $frontMatter,
                'readingTimeEstimate' => ceil(str_word_count($content) / 240) . ' min read',
            ],
        )->blade();

        return $rendered ?? $content;
    }
}
