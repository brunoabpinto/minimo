<?php

namespace App\Repositories;

use App\View\MarkdownRenderer;

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
        $files = glob("../views/pages/{$this->repository}/*.md");
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
