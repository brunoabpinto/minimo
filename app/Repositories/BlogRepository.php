<?php

namespace App\Repositories;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\MarkdownConverter;

class BlogRepository
{
    private MarkdownConverter $converter;

    public function __construct()
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new FrontMatterExtension());
        $this->converter = new MarkdownConverter($environment);
    }

    public function getPosts(): array
    {
        $mdFiles = glob('../views/pages/blog/*.md');
        $posts = [];
        foreach ($mdFiles as $mdFile) {
            $post = $this->converter->convert(file_get_contents($mdFile))->getFrontMatter();
            $post['url'] = '/blog/' . pathinfo($mdFile, PATHINFO_FILENAME);
            $posts[] = $post;
        }
        usort($posts, function ($a, $b) {
            return $b['publishDate'] <=> $a['publishDate'];
        });
        return $posts;
    }
}
