<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Vue View' }}</title>
    <link rel="stylesheet" href="/styles/app.css?v=20260211b" />
    @if (!empty($styles))
        <style>
            {!! $styles !!}
        </style>
    @endif
</head>

<body>
    @include('components.header')
    <main>
        <div id="app"></div>
    </main>
    @include('components.footer')

    <script src="{{ $vue_cdn }}"></script>
    <script>
        (function() {
            const template = {!! $template_json !!};
            const source = {!! $script_json !!};
            const initialProps = {!! $initial_props_json !!};

            function parseComponent(scriptSource) {
                if (!scriptSource || !scriptSource.trim()) {
                    return {};
                }

                if (/^\s*import\s/m.test(scriptSource)) {
                    console.error("Import statements are not supported in server-loaded .vue files.");
                    return {};
                }

                const transformed = scriptSource.replace(/\bexport\s+default\b/, "return");
                if (transformed === scriptSource) {
                    console.warn("Expected `export default` in Vue view script.");
                    return {};
                }

                try {
                    return new Function(transformed)() || {};
                } catch (error) {
                    console.error("Failed to evaluate Vue view script.", error);
                    return {};
                }
            }

            const component = parseComponent(source);
            component.template = component.template || template;

            Vue.createApp(component, initialProps).mount("#app");
        })();
    </script>
</body>

</html>
