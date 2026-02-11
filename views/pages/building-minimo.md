# I Built a Tiny PHP Framework to Stop Overthinking Side Projects

Most side projects die before the first useful screen.

Not because the idea is bad, but because setup gets heavy too early: too many folders, too many decisions, too much ceremony.

I wanted something in the middle. Not raw PHP. Not a full Laravel app either.

So I built **Minimo**.

Repository: [github.com/brunoabpinto/minimo](https://github.com/brunoabpinto/minimo)
Live preview/docs: [minimo.infinityfree.me](https://minimo.infinityfree.me/)

## What I wanted

- Convention-based routing
- A clean way to render Blade, Vue, or Markdown
- No giant bootstrap process
- Something I can explain in a few minutes

That was the whole goal.

## The core idea

Every request goes through one place:

`public/index.php -> app/Core/core.php`

From there, Minimo tries this order:

1. Match a controller method.
2. If no controller handles it, run plugins.
3. First plugin that returns content wins.

That means I can keep core behavior stable and extend rendering logic without touching routing code.

## Routing that stays out of your way

I kept URL-to-controller mapping extremely boring on purpose.

- `GET /hello` -> `HelloController::index()`
- `POST /hello` -> `HelloController::create($request)`
- `GET /post/42` -> `PostController::show(42)`

No route file required for simple cases. If naming is predictable, shipping is faster.

## Pages are just files

If no controller is found, plugins try to resolve a page from `views/pages`.

- Blade: `views/pages/docs.blade.php`
- Vue: `views/pages/docs-vue.vue`
- Markdown: `views/pages/docs-md.md`

I like this because content and UI experiments become cheap. Create one file, refresh browser, done.

## Why Markdown is built in

I write docs before polishing UI. Markdown lets me move fast and keep writing friction low.

With the Markdown plugin, a route like `/building-minimo` maps directly to this file:

`views/pages/building-minimo.md`

No extra wiring, no extra config.

## What I would improve next

- Better error pages for missing controller methods
- Plugin-level caching hooks
- A stronger test suite around route resolution

But for now, this is exactly what I wanted: a small framework that makes shipping the default.

If you are building tiny products, that trade-off is usually worth it.