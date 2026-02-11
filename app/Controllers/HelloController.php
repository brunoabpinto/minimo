<?php

namespace App\Controllers;

class HelloController
{
    public function create($request): void
    {
        $name = trim((string) ($request['name'] ?? ''));

        view()->share([
            'name' => $name,
        ]);
    }
}
