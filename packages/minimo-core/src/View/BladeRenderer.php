<?php

namespace Minimo\Core\View;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use Minimo\Core\Support\PathResolver;

final class BladeRenderer
{
    private ?Factory $factory = null;

    public function render(string $viewName, ?array $data = []): ?string
    {
        $factory = $this->resolveFactory();

        return $factory->make($viewName, $data)->render();
    }

    private function resolveFactory(): Factory
    {
        if ($this->factory !== null) {
            return $this->factory;
        }

        $this->factory = $this->createFactory();

        return $this->factory;
    }

    private function createFactory(): Factory
    {
        $basePath = PathResolver::basePath();
        $viewsPath = $basePath . '/views';
        $cachePath = $basePath . '/storage/cache/views';

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
        $factory->addExtension('blade.md', 'blade');

        return $factory;
    }
}
