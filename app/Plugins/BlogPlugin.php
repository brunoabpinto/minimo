<?php

namespace App\Plugins;

use App\Core\PluginInterface;

class BlogPlugin implements PluginInterface
{
    private const FRONT_MATTER_PATTERN = '/\A---[ \t]*\n(.*?)\n---[ \t]*\n?/s';

    public static function getPosts(): array
    {
        return (new self())->loadPosts();
    }

    public function handle(array $context): ?string
    {
        if (($context['type'] ?? '') !== 'resolve_route') {
            return null;
        }

        $route = trim((string) ($context['route'] ?? ''), '/');
        if ($route !== 'blog') {
            return null;
        }

        $posts = self::getPosts();

        $rendered = render_with_plugins([
            'type' => 'render_view',
            'view' => 'pages.blog',
            'data' => ['posts' => $posts],
        ]);

        if ($rendered !== null) {
            return $rendered;
        }

        http_response_code(404);
        return 'Blog view not found.';
    }

    private function loadPosts(): array
    {
        $blogPath = __DIR__ . '/../../views/pages/blog';
        if (!is_dir($blogPath)) {
            return [];
        }

        $files = glob($blogPath . '/*.md') ?: [];
        $posts = [];

        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }

            $source = file_get_contents($file);
            if ($source === false) {
                continue;
            }

            [$frontMatter] = $this->splitFrontMatter($source);
            $slug = pathinfo($file, PATHINFO_FILENAME);
            $dateValue = $this->frontMatterValue($frontMatter, ['publishDate', 'published', 'date']);

            $posts[] = (object) [
                'slug' => $slug,
                'url' => '/blog/' . $slug,
                'title' => $this->frontMatterValue($frontMatter, ['title']) ?: $this->titleFromSlug($slug),
                'description' => $this->nullableString($this->frontMatterValue($frontMatter, ['description', 'summary', 'excerpt'])),
                'image' => $this->nullableString($this->frontMatterValue($frontMatter, ['image', 'cover', 'cover_image', 'og:image'])),
                'publishDate' => $this->nullableString($dateValue),
                'publishTimestamp' => $this->dateToTimestamp($dateValue),
            ];
        }

        usort($posts, static function (object $a, object $b): int {
            if ($a->publishTimestamp === $b->publishTimestamp) {
                return strcmp($b->slug, $a->slug);
            }

            return $b->publishTimestamp <=> $a->publishTimestamp;
        });

        return $posts;
    }

    private function splitFrontMatter(string $source): array
    {
        $normalized = str_replace(["\r\n", "\r"], "\n", $source);

        if (!preg_match(self::FRONT_MATTER_PATTERN, $normalized, $matches)) {
            return [[], $source];
        }

        return [$this->parseFrontMatterBlock($matches[1]), substr($normalized, strlen($matches[0]))];
    }

    private function parseFrontMatterBlock(string $block): array
    {
        $metadata = [];
        $lines = explode("\n", $block);

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, ':')) {
                continue;
            }

            [$key, $value] = explode(':', $line, 2);
            $key = trim($key);
            if ($key === '') {
                continue;
            }

            $metadata[$key] = $this->trimWrappedQuotes(trim($value));
        }

        return $metadata;
    }

    private function frontMatterValue(array $frontMatter, array $keys): string
    {
        $normalized = [];

        foreach ($frontMatter as $key => $value) {
            if (!is_scalar($value)) {
                continue;
            }

            $normalized[strtolower((string) $key)] = trim((string) $value);
        }

        foreach ($keys as $key) {
            $candidate = $normalized[strtolower($key)] ?? '';
            if ($candidate !== '') {
                return $candidate;
            }
        }

        return '';
    }

    private function trimWrappedQuotes(string $value): string
    {
        $length = strlen($value);
        if ($length < 2) {
            return $value;
        }

        $first = $value[0];
        $last = $value[$length - 1];

        if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
            return substr($value, 1, -1);
        }

        return $value;
    }

    private function titleFromSlug(string $slug): string
    {
        return ucwords(str_replace(['-', '_'], ' ', $slug));
    }

    private function dateToTimestamp(string $value): int
    {
        if ($value === '') {
            return 0;
        }

        $timestamp = strtotime($value);
        return $timestamp === false ? 0 : $timestamp;
    }

    private function nullableString(string $value): ?string
    {
        $trimmed = trim($value);
        return $trimmed === '' ? null : $trimmed;
    }
}
