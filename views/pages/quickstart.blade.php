@extends('layouts.app')

@section('content')
    <section class="container">
        <h1>Quickstart</h1>
        <p>Micro, opinionated content framework for local-first preview and static deployment.</p>

        <h2>1. Install</h2>
        <pre><code>git clone https://github.com/brunoabpinto/minimo.git
cd minimo
composer install</code></pre>

        <h2>2. Create a page and a post</h2>
        <pre><code>php minimo create:page somepage
php minimo create:post somepost</code></pre>

        <h2>3. Run locally</h2>
        <pre><code>php minimo dev</code></pre>
        <p>Open <code>http://127.0.0.1:8080/somepage</code> and <code>http://127.0.0.1:8080/somepost</code>.</p>

        <h2>4. Build for deployment</h2>
        <pre><code>php minimo build</code></pre>
        <p>Deploy the contents of <code>build/</code>.</p>
    </section>
@endsection
