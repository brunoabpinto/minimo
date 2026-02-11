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
        <p>In your existing <code>HelloController</code>, read posted data in <code>create()</code> and pass it back to the view:</p>
        <pre><code>public function create($request): void
{
    $name = trim((string) ($request['name'] ?? ''));

    view()-&gt;share([
        'name' =&gt; $name,
    ]);
}</code></pre>
        <p><code>GET /hello</code> renders <code>views/pages/hello.blade.php</code> directly, and <code>POST /hello</code> sends form data to <code>create()</code>.</p>

        <h2>6. Route convention reference</h2>
        <pre><code>GET /hello          -&gt; views/pages/hello.blade.php (Blade plugin)
POST /hello         -&gt; App\Controllers\HelloController::create($request)
GET /post/comments  -&gt; App\Controllers\PostCommentsController::index()
GET /post/42        -&gt; App\Controllers\PostController::show(42)</code></pre>
        <p>Resolution order: controller first, then Blade page, then Vue page, then Markdown page.</p>

        <h2>7. Passing data to views</h2>
        <pre><code>view()-&gt;share(['name' =&gt; 'Bruno']); // current page</code></pre>
        <p>Shared variables are available in Blade as regular template variables.</p>

        <h2>8. Plugin-based view types</h2>
        <p>Minimo resolves views through plugins configured in <code>app/plugins.php</code>.</p>
        <pre><code>views/pages/docs.blade.php   -&gt; /docs
views/pages/docs-vue.vue     -&gt; /docs-vue
views/pages/docs-md.md       -&gt; /docs-md</code></pre>
    </section>
@endsection
