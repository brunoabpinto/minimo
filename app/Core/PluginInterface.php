<?php

namespace App\Core;

interface PluginInterface
{
    /**
     * Return a response string if the plugin handled the request.
     * Return null to let the next plugin (or core) handle it.
     */
    public function handle(array $context): ?string;
}
