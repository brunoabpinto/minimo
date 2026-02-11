<?php

namespace App\Core;

final class ViewResponse
{
    private string $pageName;

    public function __construct(string $pageName)
    {
        $this->pageName = trim($pageName, '/');
    }

    public function share(array $data): void
    {
        $this->render($data);
    }

    public function render(array $data = []): void
    {
        $viewName = 'pages.' . str_replace('/', '.', $this->pageName);

        $response = \render_with_plugins([
            'type' => 'render_view',
            'view' => $viewName,
            'data' => $data,
        ]);

        if ($response === null) {
            \http_response_code(404);
            echo 'View not found.';
            exit;
        }

        echo $response;
        exit;
    }
}
