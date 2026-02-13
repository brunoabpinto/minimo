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

        <p>Minimo is a lightweight PHP framework with convention-based controller routing, file-based rendering, and a built-in content CLI.</p>

        <p>Request flow:</p>
        <p><code>public/index.php</code> -> controller match -> route file (<code>.blade.php</code>/<code>.blade.md</code>/<code>.md</code>) -> 404</p>

        <h2>Directory Structure</h2>
        <ul>
            <li><code>app/Controllers</code>: HTTP controllers.</li>
            <li><code>app/Repositories</code>: data access classes.</li>
            <li><code>views/pages</code>: route files for <code>.blade.php</code>, <code>.blade.md</code>, and <code>.md</code>.</li>
            <li><code>views/layouts</code>: shared Blade layouts.</li>
            <li><code>views/layouts/markdown.blade.php</code>: Markdown layout wrapper.</li>
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

        <h2>Route File Types</h2>
        <ul>
            <li><code>/docs</code> -> <code>views/pages/docs.blade.php</code></li>
            <li><code>/test</code> -> <code>views/pages/test.blade.md</code></li>
            <li><code>/blog/hello-world</code> -> <code>views/pages/blog/hello-world.md</code></li>
        </ul>

        <h2>Blade + Markdown (.blade.md)</h2>
        <p>A <code>.blade.md</code> file is compiled as Blade first, then parsed as Markdown.</p>
        <pre><code>@@php($name = 'Bruno')

# Hello @{{ $name }}</code></pre>

        <h2>Markdown Views</h2>
        <ul>
            <li><code>/docs-md</code> -> <code>views/pages/docs-md.md</code></li>
            <li><code>/blog/hello-world</code> -> <code>views/pages/blog/hello-world.md</code></li>
        </ul>
        <p>Markdown files are parsed with front matter support and rendered through <code>views/layouts/markdown.blade.php</code>.</p>

        <h2>Minimo CLI</h2>
        <p>Use the project CLI from the repository root:</p>
        <pre><code>php minimo help
php minimo create:page somepage
php minimo create:post somepost
php minimo dev
php minimo dev 9000</code></pre>
        <p>Generated paths:</p>
        <ul>
            <li><code>create:page</code> -> <code>views/pages/somepage.blade.php</code></li>
            <li><code>create:post</code> -> <code>views/pages/somepost.md</code></li>
        </ul>
        <p><code>dev</code> starts a local server on <code>127.0.0.1:8080</code> by default. Pass a port to override.</p>
        <p>It also works seamlessly with Herd:</p>
        <pre><code>herd link myminimosite</code></pre>
        <p>Then open <code>https://myminimosite.test</code>.</p>
    </section>
@endsection
