<?php

namespace App\Controllers;

class IndexController
{
    public function index()
    {
        header('Location: /docs');
    }
}
