---
title: Minimo Framework Documentation (Markdown)
description: Core architecture and routing behavior for the current Minimo runtime.
---

<div class="docs-switch" aria-label="Documentation formats">
  <a class="docs-switch-item" href="/docs">
    <img src="https://cdn.simpleicons.org/laravel/ff2d20" alt="Laravel Blade icon" width="20" height="20" loading="lazy" decoding="async" />
    <span>Blade</span>
  </a>
  <a class="docs-switch-item is-active" href="/docs-md" aria-current="page">
    <img src="https://cdn.simpleicons.org/markdown/6b6f72" alt="Markdown icon" width="20" height="20" loading="lazy" decoding="async" />
    <span>MD</span>
  </a>
</div>

# Minimo Framework Documentation

<p class="docs-path">View path: <code>views/pages/docs-md.md</code></p>

Minimo is a lightweight PHP framework with convention-based controller routing, file-based rendering, and a built-in content CLI.

Request flow:

`public/index.php` -> controller match -> route file (`.blade.php`/`.blade.md`/`.md`) -> 404

## Directory Structure

- `app/Controllers`: HTTP controllers.
- `app/Repositories`: data access classes.
- `views/pages`: route files for `.blade.php`, `.blade.md`, and `.md`.
- `views/layouts`: shared Blade layouts.
- `views/layouts/markdown.blade.php`: Markdown layout wrapper.

## Routing

Controller class name is inferred from URL segments:

- `/hello` -> `HelloController`
- `/post/comments` -> `PostCommentsController`
- `/post/42` -> `PostController`

Method mapping:

- `GET` -> `index`
- `POST` -> `create`
- `PUT` -> `update`
- `DELETE` -> `delete`
- If a second route segment exists, method becomes `show`.

## Controller Responses

If the inferred controller method exists, its return value is passed to route view rendering.

```php
<?php

namespace App\Controllers;

class PostController
{
    public function show($slug)
    {
        return ['slug' => $slug];
    }
}
```

If no matching page file is found after controller resolution, Minimo returns a 404 page.

## Route File Types

- `/docs` -> `views/pages/docs.blade.php`
- `/test` -> `views/pages/test.blade.md`
- `/blog/hello-world` -> `views/pages/blog/hello-world.md`

## Blade + Markdown (`.blade.md`)

A `.blade.md` file is compiled as Blade first, then parsed as Markdown.

```blade
@php($name = 'Bruno')

# Hello {{ $name }}
```

## Markdown Views

- `/docs-md` -> `views/pages/docs-md.md`
- `/blog/hello-world` -> `views/pages/blog/hello-world.md`

Markdown files are parsed with front matter support and rendered through `views/layouts/markdown.blade.php`.

## Minimo CLI

Use the project CLI from the repository root:

```bash
php minimo help
php minimo create:page somepage
php minimo create:post somepost
php minimo create:controller FooBarController
php minimo create:controller TestController --md
php minimo create:controller TestController --blade
php minimo dev
php minimo dev 9000
```

Generated paths:

- `create:page` -> `views/pages/somepage.blade.php`
- `create:post` -> `views/pages/somepost.md`
- `create:controller` -> `app/Controllers/FooBarController.php`
- `create:controller TestController --md` -> `app/Controllers/TestController.php` + `views/pages/test.blade.md`
- `create:controller TestController --blade` -> `app/Controllers/TestController.php` + `views/pages/test.blade.php`

`dev` starts a local server on `127.0.0.1:8080` by default. Pass a port to override.

It also works seamlessly with Herd:

```bash
herd link myminimosite
```

Then open `https://myminimosite.test`.
