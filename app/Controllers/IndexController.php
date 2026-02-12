<?php

namespace App\Controllers;

class IndexController
{
    public function index()
    {
        exit(header('Location: /docs'));
    }
}
