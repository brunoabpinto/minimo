<?php

namespace App\Repositories;

use Minimo\Core\View\MarkdownRenderer;

class ContentRepository
{
    private string $repository;

    public function __construct(string $repository)
    {
        $this->repository = $repository;
    }

    public function getPosts(): array
    {
        $renderer = new MarkdownRenderer();
        $files = glob(__DIR__ . "/../../views/pages/{$this->repository}/*.md");
        $posts = [];
        foreach ($files as $file) {
            $post = $renderer->getPost($file);
            $posts[] = $post;
        }
        usort($posts, function ($a, $b) {
            return $b['publishDate'] <=> $a['publishDate'];
        });
        return $posts;
    }
}
