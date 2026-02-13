@extends('layouts.app')

@section('content')
    <section class="container">
        <h1>Quickstart</h1>
        <p>Micro, opinionated content framework for local-first preview and static deployment.</p>

        <h2>1. Install</h2>
        <pre><code>git clone https://github.com/brunoabpinto/minimo.git
cd minimo
composer install
npm install</code></pre>

        <h2>2. Create a page and a post</h2>
        <pre><code>php minimo create:page somepage
php minimo create:post somepost</code></pre>

        <h2>3. Run locally</h2>
        <pre><code>php minimo dev</code></pre>
        <p><code>dev</code> starts PHP + Vite with live refresh on save.</p>
        <p>Open <code>http://127.0.0.1:8080/somepage</code> and <code>http://127.0.0.1:8080/somepost</code>.</p>

        <h2>4. Build for deployment</h2>
        <pre><code>php minimo build</code></pre>
        <p>Build output uses pretty URLs (<code>build/somepage/index.html</code>).</p>

        <h2>5. Preview static output</h2>
        <pre><code>php minimo preview</code></pre>
        <p>This runs <code>build</code> and serves <code>build/</code> on <code>http://127.0.0.1:9090</code>.</p>
    </section>
@endsection
