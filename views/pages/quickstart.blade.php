@extends('layouts.app')

@section('content')
    <section class="container">
        <h1>Quickstart</h1>
        <p>Get Minimo running locally in a few minutes.</p>

        <h2>1. Clone the repository</h2>
        <pre><code>git clone https://github.com/brunoabpinto/minimo.git
cd minimo</code></pre>

        <h2>2. Install dependencies</h2>
        <pre><code>composer install</code></pre>

        <h2>3. Start the local server</h2>
        <pre><code>php -S localhost:8000 -t public</code></pre>
        <p>Open <code>http://localhost:8000</code> in your browser.</p>

        <h2>4. Create a Hello page with a form</h2>
        <p>Create <code>views/pages/hello.blade.php</code>:</p>
        <pre><code>@@extends('layouts.app')

@@section('content')
    &lt;section class="container"&gt;
        &lt;h1&gt;Hello World&lt;/h1&gt;
        &lt;p&gt;Send a value to the controller and render it back.&lt;/p&gt;

        &lt;form method="POST" action="/hello"&gt;
            &lt;label for="name"&gt;Your name&lt;/label&gt;
            &lt;input id="name" name="name" type="text" value="@{{ $name ?? '' }}" /&gt;
            &lt;button type="submit"&gt;Send to controller&lt;/button&gt;
        &lt;/form&gt;

        @@if (!empty($name))
            &lt;p&gt;Hello, &lt;strong&gt;@{{ $name }}&lt;/strong&gt;!&lt;/p&gt;
        @@endif
    &lt;/section&gt;
@@endsection</code></pre>
        <p>Open <code>/hello</code>.</p>

        <h2>5. Use your existing controller</h2>
        <p>In your existing <code>HelloController</code>, read posted data in <code>create()</code> and return it as view data:</p>
        <pre><code>&lt;?php

namespace App\Controllers;

class HelloController
{
    public function create($request): array
    {
        $name = trim((string) ($request['name'] ?? ''));

        return ['name' =&gt; $name];
    }
}</code></pre>
        <p><code>POST /hello</code> sends form data to <code>create($request)</code>, and the returned array is passed to <code>views/pages/hello.blade.php</code>.</p>

        <h2>6. Route convention reference</h2>
        <pre><code>GET /hello          -&gt; App\Controllers\HelloController::index() (if method exists)
POST /hello         -&gt; App\Controllers\HelloController::create($request)
GET /post/comments  -&gt; App\Controllers\PostCommentsController::show('comments')
GET /post/42        -&gt; App\Controllers\PostController::show(42)</code></pre>
        <p>Resolution order: controller method (if class + method exists), then route file in <code>views/pages</code> (<code>.blade.php</code> first, then <code>.md</code>), then 404.</p>

        <h2>7. Passing data to views</h2>
        <pre><code>public function show($slug): array
{
    return ['slug' =&gt; $slug];
}</code></pre>
        <p>Controller arrays are passed to matching route views as regular template variables.</p>

        <h2>8. Content route types</h2>
        <pre><code>views/pages/docs.blade.php         -&gt; /docs
views/pages/docs-md.md             -&gt; /docs-md
views/pages/blog/hello-world.md    -&gt; /blog/hello-world</code></pre>
        <p>Markdown files can include front matter and are wrapped by <code>views/layouts/markdown.blade.php</code>.</p>
    </section>
@endsection
