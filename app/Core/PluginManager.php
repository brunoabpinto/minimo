<?php

namespace App\Core;

use InvalidArgumentException;

class PluginManager
{
    /** @var PluginInterface[] */
    private array $plugins;

    /**
     * @param PluginInterface[] $plugins
     */
    public function __construct(array $plugins)
    {
        $this->plugins = $plugins;
    }

    public static function fromConfig(string $configPath): self
    {
        if (!file_exists($configPath)) {
            return new self([]);
        }

        $classes = require $configPath;
        if (!is_array($classes)) {
            throw new InvalidArgumentException('Plugin config must return an array of class names.');
        }

        $plugins = [];
        foreach ($classes as $class) {
            if (!is_string($class) || $class === '') {
                continue;
            }
            if (!class_exists($class)) {
                continue;
            }
            $instance = new $class();
            if ($instance instanceof PluginInterface) {
                $plugins[] = $instance;
            }
        }

        return new self($plugins);
    }

    public function handle(array $context): ?string
    {
        foreach ($this->plugins as $plugin) {
            $response = $plugin->handle($context);
            if ($response !== null) {
                return $response;
            }
        }

        return null;
    }
}
