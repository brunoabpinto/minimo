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

Minimo is a lightweight PHP framework with convention-based controller routing and file-based rendering.

Request flow:

`public/index.php` -> `app/Core/core.php` -> controller match -> Blade/Markdown route file -> 404

## Directory Structure

- `app/Controllers`: HTTP controllers.
- `app/Core`: framework request and routing core.
- `app/Repositories`: data access classes.
- `app/View`: Blade and Markdown renderers.
- `views/pages`: route files for `.blade.php` and `.md`.
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

## Blade Views

- `/docs` -> `views/pages/docs.blade.php`
- `/about` -> `views/pages/about.blade.php`
- `/foo/bar` -> `views/pages/foo/bar.blade.php`

## Markdown Views

- `/blog/hello-world` -> `views/pages/blog/hello-world.md`
- `/docs-md` -> `views/pages/docs-md.md`

Markdown files are parsed with front matter support and rendered through `views/layouts/markdown.blade.php`.
