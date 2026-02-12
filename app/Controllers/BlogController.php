<?php

namespace App\Controllers;

use App\Repositories\BlogRepository;

class BlogController
{

    public function index()
    {
        $posts = (new BlogRepository())->getPosts();
        return compact('posts');
    }
}
