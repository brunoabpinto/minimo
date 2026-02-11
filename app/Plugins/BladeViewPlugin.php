<?php

namespace App\Plugins;

use App\Core\PluginInterface;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

class BladeViewPlugin implements PluginInterface
{
    private ?Factory $factory = null;

    public function handle(array $context): ?string
    {
        $type = $context['type'] ?? 'resolve_route';

        if ($type === 'render_view') {
            return $this->renderNamedView(
                (string) ($context['view'] ?? ''),
                is_array($context['data'] ?? null) ? $context['data'] : []
            );
        }

        if ($type === 'resolve_route') {
            return $this->renderRoute($context);
        }

        return null;
    }

    private function renderRoute(array $context): ?string
    {
        $route = trim((string) ($context['route'] ?? ''), '/');
        $pageKeys = $this->routeToPageKeys($route);

        if ($pageKeys === null) {
            return null;
        }

        foreach ($pageKeys as $pageKey) {
            $viewName = 'pages.' . str_replace('/', '.', $pageKey);
            $rendered = $this->renderNamedView($viewName, []);
            if ($rendered !== null) {
                return $rendered;
            }
        }

        return null;
    }

    private function routeToPageKeys(string $route): ?array
    {
        if ($route === '' || str_contains($route, '..')) {
            return null;
        }

        if (!preg_match('~^[a-zA-Z0-9/_-]+$~', $route)) {
            return null;
        }

        $keys = [$route];
        $flatRoute = str_replace('/', '-', $route);
        if ($flatRoute !== $route) {
            $keys[] = $flatRoute;
        }

        return $keys;
    }

    private function renderNamedView(string $viewName, array $data): ?string
    {
        $factory = $this->resolveFactory();
        if ($factory === null || $viewName === '' || !$factory->exists($viewName)) {
            return null;
        }

        return $factory->make($viewName, $data)->render();
    }

    private function resolveFactory(): ?Factory
    {
        if ($this->factory !== null) {
            return $this->factory;
        }

        if (!class_exists(Container::class) || !class_exists(Dispatcher::class) || !class_exists(Filesystem::class) || !class_exists(BladeCompiler::class)) {
            return null;
        }

        $this->factory = $this->createFactory();
        return $this->factory;
    }

    private function createFactory(): Factory
    {
        $viewsPath = __DIR__ . '/../../views';
        $cachePath = __DIR__ . '/../../storage/cache/views';

        if (!is_dir($cachePath)) {
            mkdir($cachePath, 0777, true);
        }

        $filesystem = new Filesystem();
        $container = new Container();
        $dispatcher = new Dispatcher($container);
        $resolver = new EngineResolver();
        $bladeCompiler = new BladeCompiler($filesystem, $cachePath);

        $resolver->register('blade', function () use ($bladeCompiler) {
            return new CompilerEngine($bladeCompiler);
        });

        $finder = new FileViewFinder($filesystem, [$viewsPath]);
        $factory = new Factory($resolver, $finder, $dispatcher);
        $factory->addExtension('blade.php', 'blade');

        return $factory;
    }
}
