<?php

namespace App\Controllers;

use App\Repositories\ContentRepository;

class BlogController
{

    public function index()
    {
        $repository = new ContentRepository('blog');
        return ['posts' => $repository->getPosts()];
    }
}
