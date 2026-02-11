<div class="docs-switch" aria-label="Documentation formats">
  <a class="docs-switch-item" href="/docs">
    <img src="https://cdn.simpleicons.org/laravel/ff2d20" alt="Laravel Blade icon" width="20" height="20" loading="lazy" decoding="async" />
    <span>Blade</span>
  </a>
  <a class="docs-switch-item" href="/docs-vue">
    <img src="https://cdn.simpleicons.org/vuedotjs/42b883" alt="Vue icon" width="20" height="20" loading="lazy" decoding="async" />
    <span>Vue</span>
  </a>
  <a class="docs-switch-item is-active" href="/docs-md" aria-current="page">
    <img src="https://cdn.simpleicons.org/markdown/6b6f72" alt="Markdown icon" width="20" height="20" loading="lazy" decoding="async" />
    <span>MD</span>
  </a>
</div>

# Minimo Framework Documentation

<p class="docs-path">View path: <code>views/pages/docs-md.md</code></p>

Minimo is a lightweight PHP framework with controller-based routing and a plugin pipeline for rendering.

Request flow:

`public/index.php` -> `app/Core/core.php` -> controller match or plugin pipeline

## Directory Structure

- `app/Controllers`: HTTP controllers.
- `app/Core`: framework core (routing and plugin contracts/manager).
- `app/Plugins`: rendering and feature plugins.
- `views/pages`: route files for `.blade.php`, `.vue`, and `.md`.
- `views/markdown`: markdown layout wrapper.
- `views/vue`: Vue layout wrapper.

## Routing

Example:

- `GET /post/comments` -> `PostCommentsController@index`

Method mapping:

- `GET` -> `index`
- `POST` -> `create`
- `PUT` -> `update`
- `DELETE` -> `delete`
- `/{resource}/{id}` -> `show($id)` when second segment is numeric

If no controller is matched, plugins handle route resolution.

## Controllers

Controllers live in `app/Controllers` and are plain PHP classes.

Example:

```php
<?php

namespace App\Controllers;

class DocsController
{
    public function index()
    {
        return "Docs endpoint";
    }

    public function show($id)
    {
        return "Showing {$id}";
    }
}
```

Controller response behavior:

- If a controller method returns non-null, core echoes it and exits.
- You can also render Blade with `view([...])`.

## Blade Views

Route-based Blade views:

- `/docs` -> `views/pages/docs.blade.php`
- `/foo/bar` -> `views/pages/foo-bar.blade.php`

Controller-based Blade rendering:

```php
public function index()
{
    view(['title' => 'Hello']); // resolves to views/pages/{controller}.blade.php
}
```

Layout pattern:

- `views/layouts/app.blade.php` includes shared header/footer.

## Markdown Views

Markdown plugin resolution:

- `/blogpost` -> `views/pages/blogpost.md`

The plugin parses markdown and renders it through:

- `views/markdown/layout.blade.php`

## Vue Views

Vue plugin resolution:

- `/hello` -> `views/pages/hello.vue`

How it works:

- Reads `<template>`, `<script>`, and `<style>` from the `.vue` file.
- Wraps output in `views/vue/layout.blade.php`.
- Loads Vue 3 from CDN and mounts the component.

Notes:

- Expected script format: `export default { ... }`.
- `script setup` is not supported in the current runtime loader.

## Plugin System

Plugins implement `App\Core\PluginInterface`.

Register plugins in `app/plugins.php`:

```php
return [
    App\Plugins\BladeViewPlugin::class,
    App\Plugins\VueViewPlugin::class,
    App\Plugins\MarkdownPlugin::class,
];
```

Pipeline behavior:

- Return `null` to pass to the next plugin.
- Return a string to mark the request as handled.
