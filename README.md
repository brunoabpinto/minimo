# Minimo

Live preview/docs: [minimo.infinityfree.me](https://minimo.infinityfree.me/)

Minimo is a lightweight PHP framework with:
- convention-based controller routing
- file-based route rendering for `.blade.php`, `.blade.md`, and `.md`
- a built-in `minimo` CLI for content scaffolding

## Requirements

- PHP 8.3+
- Composer

## Install

```bash
git clone https://github.com/brunoabpinto/minimo.git
cd minimo
composer install
```

## Run

```bash
php minimo dev
```

Open `http://127.0.0.1:8080`.

To use a custom port:

```bash
php minimo dev 9000
```

Works seamlessly with Herd as well:

```bash
herd link myminimosite
```

Then open `https://myminimosite.test`.

## Request Flow

Requests resolve in this order:

1. Controller method (if class + method exists)
2. Route file in `views/pages` (`.blade.php`, `.blade.md`, or `.md`)
3. `404 Not found.`

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
- Blade + Markdown: `views/pages/<route-key>.blade.md`
- Markdown: `views/pages/<route-key>.md`

`<route-key>` is the route path as-is (supports nested directories).

Examples:

- `/docs` -> `views/pages/docs.blade.php`
- `/test` -> `views/pages/test.blade.md`
- `/docs-md` -> `views/pages/docs-md.md`
- `/foo/bar` -> `views/pages/foo/bar.blade.php` (or `.md`)

Markdown pages are wrapped by `views/layouts/markdown.blade.php`.

`.blade.md` files are rendered as Blade first, then parsed as Markdown through the same markdown layout.

## Minimo CLI

Run from the project root:

```bash
php minimo help
php minimo create:page somepage
php minimo create:post somepost
php minimo dev
php minimo dev 9000
```

Generated files:

- `create:page` -> `views/pages/somepage.blade.php`
- `create:post` -> `views/pages/somepost.md`

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
