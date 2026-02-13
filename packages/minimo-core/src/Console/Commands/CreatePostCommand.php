<?php

namespace Minimo\Core\Console\Commands;

final class CreatePostCommand
{
    public function __construct(private string $basePath) {}

    public function handle(array $args): int
    {
        if (count($args) !== 1) {
            fwrite(STDERR, "Usage: minimo create:post <slug>" . PHP_EOL);

            return 1;
        }

        $slug = $this->normalizeSlug($args[0]);

        if ($slug === '') {
            fwrite(STDERR, "Missing slug. Usage: minimo create:post <slug>" . PHP_EOL);

            return 1;
        }

        if (!preg_match('/^[a-z0-9][a-z0-9\\/-]*$/', $slug) || str_contains($slug, '..')) {
            fwrite(STDERR, "Invalid slug. Use lowercase letters, numbers, dashes, and optional path segments." . PHP_EOL);

            return 1;
        }

        $relativePath = "views/pages/{$slug}.md";
        $fullPath = $this->basePath . DIRECTORY_SEPARATOR . $relativePath;
        $directory = dirname($fullPath);

        if (file_exists($fullPath)) {
            fwrite(STDERR, "File already exists: {$relativePath}" . PHP_EOL);

            return 1;
        }

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $title = $this->titleFromSlug($slug);
        $content = $this->contentTemplate($title);

        file_put_contents($fullPath, $content);
        echo "Created {$relativePath}" . PHP_EOL;

        return 0;
    }

    private function normalizeSlug(string $slug): string
    {
        $slug = trim($slug);
        $slug = str_replace('\\', '/', $slug);
        $slug = trim($slug, '/');

        if (str_ends_with($slug, '.md')) {
            $slug = substr($slug, 0, -3);
        }

        return $slug;
    }

    private function titleFromSlug(string $slug): string
    {
        $base = basename($slug);
        $words = str_replace('-', ' ', strtolower($base));

        return ucfirst($words);
    }

    function generateImage(): string
    {
        $img = imagecreatetruecolor(1, 1);

        $color = imagecolorallocate($img, rand(0, 255), rand(0, 255), rand(0, 255));
        imagesetpixel($img, 0, 0, $color);

        ob_start();
        imagepng($img);
        $data = ob_get_clean();

        imagedestroy($img);

        return "data:image/png;base64," . base64_encode($data);
    }

    private function contentTemplate(string $title): string
    {
        $date = date('Y-m-d');
        return <<<MD
        ---
        title: {$title}
        description: Write something amazing
        image: {$this->generateImage()}
        publishDate: {$date}
        ---

        # {$title}

        Write something amazing.
        MD;
    }
}
