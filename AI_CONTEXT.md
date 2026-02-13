# Minimo AI Context

This file is the canonical project context for AI/code agents.

## Project concept

Minimo is a micro, opinionated, content-first PHP framework.

Think of it as Astro-like in workflow:

- local run is required for previewing content and validating behavior
- static build output is the deployment artifact

The project prioritizes:

- very small surface area
- clear conventions over configuration
- simple code over abstractions

## Core workflow (must match docs and behavior)

1. Create content/routes.
2. Run locally with `php minimo dev` and preview.
3. Build static output with `php minimo build`.
4. Deploy the `build/` folder.

## Runtime conventions

- Entry: `public/index.php` -> `app/Core/core.php` -> `Minimo\Core\Kernel`.
- Controller class is inferred from URL segments.
- Method mapping:
  - `GET` => `index`
  - `POST` => `create`
  - `PUT` => `update`
  - `DELETE` => `delete`
  - second segment forces `show`
- Route views are file-based in `views/pages`.

## Supported route file types

- `*.blade.php`
- `*.blade.md`
- `*.md`

Rendering behavior:

- `.blade.php`: Blade render
- `.md`: Markdown render with front matter support
- `.blade.md`: Blade first, then Markdown

## CLI behavior

Use `php minimo ...`.

Supported commands:

- `create:page <name>`
- `create:post <slug>`
- `create:controller <Name> [--md|--blade]`
- `dev [port]`
- `build`

`create:controller`:

- always creates `app/Controllers/<Name>.php`
- `--blade` also creates `views/pages/<route>.blade.php`
- `--md` also creates `views/pages/<route>.blade.md`

## Build command expectation

Keep `build` simple:

- list route files from `views/pages`
- render each route
- write corresponding `.html` files into `build/`

Do not over-engineer this command.

## Engineering style (important)

- prefer direct, readable code
- avoid creating many helper files for simple tasks
- avoid duplicate logic when a direct existing path can be reused
- keep changes narrowly scoped

## Docs policy

Only update docs when explicitly requested by the user.
Do not auto-update docs after every code change.

## Current source of truth files

- runtime: `packages/minimo-core/src/Kernel.php`
- rendering: `packages/minimo-core/src/View/*`
- CLI: `packages/minimo-core/src/Console/*`
- app controllers/repositories: `app/*`
- routes/content: `views/pages/*`
