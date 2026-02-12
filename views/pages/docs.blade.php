@extends('layouts.app')

@section('content')
    <section class="docs container">
        <div class="docs-switch" aria-label="Documentation formats">
            <a class="docs-switch-item is-active" href="/docs" aria-current="page">
                <img src="https://cdn.simpleicons.org/laravel/ff2d20" alt="Laravel Blade icon" width="20" height="20"
                    loading="lazy" decoding="async" />
                <span>Blade</span>
            </a>
            <a class="docs-switch-item" href="/docs-md">
                <img src="https://cdn.simpleicons.org/markdown/6b6f72" alt="Markdown icon" width="20" height="20"
                    loading="lazy" decoding="async" />
                <span>MD</span>
            </a>
        </div>

        <h1>Minimo Framework Documentation</h1>
        <p class="docs-path">View path: <code>views/pages/docs.blade.php</code></p>

        <p>Minimo is a lightweight PHP framework with convention-based controller routing and file-based rendering.</p>

        <p>Request flow:</p>
        <p><code>public/index.php</code> -> <code>app/Core/core.php</code> -> controller match -> Blade/Markdown route file -> 404</p>

        <h2>Directory Structure</h2>
        <ul>
            <li><code>app/Controllers</code>: HTTP controllers.</li>
            <li><code>app/Core</code>: framework request and routing core.</li>
            <li><code>app/Repositories</code>: data access classes.</li>
            <li><code>app/View</code>: Blade and Markdown renderers.</li>
            <li><code>views/pages</code>: route files for <code>.blade.php</code> and <code>.md</code>.</li>
            <li><code>views/layouts</code>: shared Blade layouts.</li>
            <li><code>views/markdown</code>: Markdown layout wrapper.</li>
        </ul>

        <h2>Routing</h2>
        <p>Controller class name is inferred from URL segments:</p>
        <ul>
            <li><code>/hello</code> -> <code>HelloController</code></li>
            <li><code>/post/comments</code> -> <code>PostCommentsController</code></li>
            <li><code>/post/42</code> -> <code>PostController</code></li>
        </ul>

        <p>Method mapping:</p>
        <ul>
            <li><code>GET</code> -> <code>index</code></li>
            <li><code>POST</code> -> <code>create</code></li>
            <li><code>PUT</code> -> <code>update</code></li>
            <li><code>DELETE</code> -> <code>delete</code></li>
            <li>If a second route segment exists, method becomes <code>show</code>.</li>
        </ul>

        <h2>Controller Responses</h2>
        <p>If the inferred controller method exists, its return value is passed to route view rendering.</p>

        <p>Example:</p>
        <pre><code>&lt;?php

namespace App\Controllers;

class PostController
{
    public function show($slug)
    {
        return ['slug' =&gt; $slug];
    }
}</code></pre>

        <p>If no matching page file is found after controller resolution, Minimo returns a 404 page.</p>

        <h2>Blade Views</h2>
        <ul>
            <li><code>/docs</code> -> <code>views/pages/docs.blade.php</code></li>
            <li><code>/about</code> -> <code>views/pages/about.blade.php</code></li>
            <li><code>/foo/bar</code> -> <code>views/pages/foo/bar.blade.php</code></li>
        </ul>

        <h2>Markdown Views</h2>
        <ul>
            <li><code>/blog/hello-world</code> -> <code>views/pages/blog/hello-world.md</code></li>
            <li><code>/docs-md</code> -> <code>views/pages/docs-md.md</code></li>
        </ul>

        <p>Markdown files are parsed with front matter support and rendered through <code>views/markdown/layout.blade.php</code>.</p>
    </section>
@endsection
