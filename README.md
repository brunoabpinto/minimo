# Minimo

Live preview/docs: [minimo.infinityfree.me](https://minimo.infinityfree.me/)

Minimo is a lightweight PHP framework with:
- convention-based controller routing
- file-based route rendering
- Blade and Markdown page support

## Requirements

- PHP 8.3+
- Composer

## Install

```bash
composer install
```

## Run

```bash
php -S localhost:8000 -t public
```

Open `http://localhost:8000`.

## Request Flow

`public/index.php` loads `app/Core/core.php`, then resolves in this order:

1. Controller method (if class + method exists)
2. Blade page file in `views/pages`
3. Markdown page file in `views/pages`
4. `404 Not found.`

## Routing Conventions

Controller class is inferred from URL segments:

- `GET /hello` -> `App\Controllers\HelloController::index()` (if method exists)
- `POST /hello` -> `App\Controllers\HelloController::create($request)`
- `GET /post/comments` -> `App\Controllers\PostCommentsController::show('comments')`
- `GET /post/42` -> `App\Controllers\PostController::show(42)`

HTTP verb to method mapping:

- `GET` -> `index`
- `POST` -> `create`
- `PUT` -> `update`
- `DELETE` -> `delete`
- if a second segment exists, method becomes `show`

## Page Files

Route files live in `views/pages`:

- Blade: `views/pages/<route-key>.blade.php`
- Markdown: `views/pages/<route-key>.md`

`<route-key>` is the route path as-is (supports nested directories).

Examples:

- `/docs` -> `views/pages/docs.blade.php`
- `/docs-md` -> `views/pages/docs-md.md`
- `/foo/bar` -> `views/pages/foo/bar.blade.php` (or `.md`)

Markdown pages are wrapped by `views/layouts/markdown.blade.php`.

## Controller Response Data

Inside a controller:

```php
return ['name' => 'Bruno'];
```

If a matching route view exists, returned arrays are passed to that Blade/Markdown render.

## Current Example Routes

- `/` -> redirects to `/docs` (`IndexController`)
- `/docs` -> Blade docs page
- `/docs-md` -> Markdown docs page
- `/about` -> About page
- `/blog/hello-world` -> Markdown blog post page
