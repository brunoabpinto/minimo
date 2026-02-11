<template>
  <section class="container docs">
    <div class="docs-switch" aria-label="Documentation formats">
      <a class="docs-switch-item" href="/docs">
        <img
          src="https://cdn.simpleicons.org/laravel/ff2d20"
          alt="Laravel Blade icon"
          width="20"
          height="20"
          loading="lazy"
          decoding="async"
        />
        <span>Blade</span>
      </a>
      <a
        class="docs-switch-item is-active"
        href="/docs-vue"
        aria-current="page"
      >
        <img
          src="https://cdn.simpleicons.org/vuedotjs/42b883"
          alt="Vue icon"
          width="20"
          height="20"
          loading="lazy"
          decoding="async"
        />
        <span>Vue</span>
      </a>
      <a class="docs-switch-item" href="/docs-md">
        <img
          src="https://cdn.simpleicons.org/markdown/6b6f72"
          alt="Markdown icon"
          width="20"
          height="20"
          loading="lazy"
          decoding="async"
        />
        <span>MD</span>
      </a>
    </div>

    <h1>Minimo Framework Documentation</h1>
    <p class="docs-path">View path: <code>views/pages/docs-vue.vue</code></p>

    <p>
      Minimo is a lightweight PHP framework with controller-based routing and
      a plugin pipeline for rendering.
    </p>

    <p>Request flow:</p>
    <p>
      <code>public/index.php</code> -> <code>app/Core/core.php</code> ->
      controller match or plugin pipeline
    </p>

    <h2>Directory Structure</h2>
    <ul>
      <li><code>app/Controllers</code>: HTTP controllers.</li>
      <li>
        <code>app/Core</code>: framework core (routing and plugin
        contracts/manager).
      </li>
      <li><code>app/Plugins</code>: rendering and feature plugins.</li>
      <li>
        <code>views/pages</code>: route files for <code>.blade.php</code>,
        <code>.vue</code>, and <code>.md</code>.
      </li>
      <li><code>views/markdown</code>: markdown layout wrapper.</li>
      <li><code>views/vue</code>: Vue layout wrapper.</li>
    </ul>

    <h2>Routing</h2>
    <p>Example:</p>
    <ul>
      <li>
        <code>GET /post/comments</code> ->
        <code>PostCommentsController@index</code>
      </li>
    </ul>

    <p>Method mapping:</p>
    <ul>
      <li><code>GET</code> -> <code>index</code></li>
      <li><code>POST</code> -> <code>create</code></li>
      <li><code>PUT</code> -> <code>update</code></li>
      <li><code>DELETE</code> -> <code>delete</code></li>
      <li>
        <code>/{resource}/{id}</code> -> <code>show($id)</code> when second
        segment is numeric
      </li>
    </ul>

    <p>If no controller is matched, plugins handle route resolution.</p>

    <h2>Controllers</h2>
    <p>
      Controllers live in <code>app/Controllers</code> and are plain PHP
      classes.
    </p>

    <p>Example:</p>
    <pre><code>&lt;?php

namespace App\Controllers;

class DocsController
{
    public function index()
    {
        return "Docs endpoint";
    }

    public function show($id)
    {
        return "Showing {$id}";
    }
}</code></pre>

    <p>Controller response behavior:</p>
    <ul>
      <li>
        If a controller method returns non-null, core echoes it and exits.
      </li>
      <li>You can also render Blade with <code>view([...])</code>.</li>
    </ul>

    <h2>Blade Views</h2>
    <p>Route-based Blade views:</p>
    <ul>
      <li><code>/docs</code> -> <code>views/pages/docs.blade.php</code></li>
      <li>
        <code>/foo/bar</code> -> <code>views/pages/foo-bar.blade.php</code>
      </li>
    </ul>

    <p>Controller-based Blade rendering:</p>
    <pre><code>public function index()
{
    view(['title' => 'Hello']); // resolves to views/pages/{controller}.blade.php
}</code></pre>

    <p>Layout pattern:</p>
    <ul>
      <li>
        <code>views/layouts/app.blade.php</code> includes shared header/footer.
      </li>
    </ul>

    <h2>Markdown Views</h2>
    <p>Markdown plugin resolution:</p>
    <ul>
      <li><code>/blogpost</code> -> <code>views/pages/blogpost.md</code></li>
    </ul>

    <p>The plugin parses markdown and renders it through:</p>
    <ul>
      <li><code>views/markdown/layout.blade.php</code></li>
    </ul>

    <h2>Vue Views</h2>
    <p>Vue plugin resolution:</p>
    <ul>
      <li><code>/hello</code> -> <code>views/pages/hello.vue</code></li>
    </ul>

    <p>How it works:</p>
    <ul>
      <li>
        Reads <code>&lt;template&gt;</code>, <code>&lt;script&gt;</code>, and
        <code>&lt;style&gt;</code> from the <code>.vue</code> file.
      </li>
      <li>Wraps output in <code>views/vue/layout.blade.php</code>.</li>
      <li>Loads Vue 3 from CDN and mounts the component.</li>
    </ul>

    <p>Notes:</p>
    <ul>
      <li>Expected script format: <code>export default { ... }</code>.</li>
      <li>
        <code>script setup</code> is not supported in the current runtime
        loader.
      </li>
    </ul>

    <h2>Plugin System</h2>
    <p>Plugins implement <code>App\Core\PluginInterface</code>.</p>

    <p>Register plugins in <code>app/plugins.php</code>:</p>
    <pre><code>return [
    App\Plugins\BladeViewPlugin::class,
    App\Plugins\VueViewPlugin::class,
    App\Plugins\MarkdownPlugin::class,
];</code></pre>

    <p>Pipeline behavior:</p>
    <ul>
      <li>Return <code>null</code> to pass to the next plugin.</li>
      <li>Return a string to mark the request as handled.</li>
    </ul>
  </section>
</template>

<script>
export default {};
</script>

<style>
.docs code {
  word-break: break-word;
}
</style>
