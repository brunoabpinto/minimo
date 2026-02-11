# Minimo

Minimo is a lightweight PHP framework with:
- convention-based controller routing
- plugin-driven route rendering
- Blade, Vue, and Markdown page support

## Requirements

- PHP 8.2+
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
2. Blade page plugin
3. Vue page plugin
4. Markdown page plugin
5. `404 Not found.`

## Routing Conventions

Controller class is inferred from URL segments:

- `GET /hello` -> `App\Controllers\HelloController::index()` (if method exists)
- `POST /hello` -> `App\Controllers\HelloController::create($request)`
- `GET /post/comments` -> `App\Controllers\PostCommentsController::index()`
- `GET /post/42` -> `App\Controllers\PostController::show(42)`

HTTP verb to method mapping:

- `GET` -> `index`
- `POST` -> `create`
- `PUT` -> `update`
- `DELETE` -> `delete`
- if second segment is numeric, method becomes `show`

## Page Files

Route files live in `views/pages`:

- Blade: `views/pages/<route-key>.blade.php`
- Vue: `views/pages/<route-key>.vue`
- Markdown: `views/pages/<route-key>.md`

`<route-key>` is the route path with `/` replaced by `-`.

Examples:

- `/docs` -> `views/pages/docs.blade.php`
- `/docs-vue` -> `views/pages/docs-vue.vue`
- `/docs-md` -> `views/pages/docs-md.md`
- `/foo/bar` -> `views/pages/foo-bar.blade.php` (or `.vue` / `.md`)

Markdown pages are wrapped by `views/markdown/layout.blade.php`.
Vue pages are wrapped by `views/vue/layout.blade.php`.

## Controller View Helper

Inside a controller:

```php
view()->share(['name' => 'Bruno']);
```

Or:

```php
view(['name' => 'Bruno']);
```

Both render the page inferred from the controller name (for `HelloController`, it renders `views/pages/hello.blade.php`).

## Plugins

Plugin order is configured in `app/plugins.php`.
Each plugin implements `App\Core\PluginInterface`.

Register it by adding the class name to `app/plugins.php`.

## Current Example Routes

- `/` -> redirects to `/docs` (`IndexController`)
- `/docs` -> Blade docs page
- `/docs-vue` -> Vue docs page
- `/docs-md` -> Markdown docs page
- `/quickstart` -> Quickstart page
- `/about` -> About page
- `/hello` -> Hello form page (`GET`) + form submit handler (`POST`)
