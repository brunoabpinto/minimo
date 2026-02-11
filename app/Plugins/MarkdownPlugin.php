<?php

namespace App\Plugins;

use App\Core\PluginInterface;
use League\CommonMark\CommonMarkConverter;

class MarkdownPlugin implements PluginInterface
{
    private const FRONT_MATTER_PATTERN = '/\A---[ \t]*\n(.*?)\n---[ \t]*\n?/s';

    public function handle(array $context): ?string
    {
        if (($context['type'] ?? '') !== 'resolve_route') {
            return null;
        }

        $route = trim((string) ($context['route'] ?? ''), '/');
        $pageKeys = $this->routeToPageKeys($route);
        if ($pageKeys === null) {
            return null;
        }

        $md = $this->resolvePagePath($pageKeys, '.md');
        if ($md === null) {
            return null;
        }

        $source = file_get_contents($md);
        if ($source === false) {
            return null;
        }

        [$frontMatter, $markdown] = $this->splitFrontMatter($source);

        $siteTitle = $this->siteTitleFromConfig();
        $markdownTitle = $this->frontMatterValue($frontMatter, ['title']);
        $pageTitle = $siteTitle;

        if ($markdownTitle !== '') {
            $pageTitle = $siteTitle !== '' ? $siteTitle . ' - ' . $markdownTitle : $markdownTitle;
        }

        $metaDescription = $this->frontMatterValue($frontMatter, [
            'description',
            'meta_description',
            'meta-description',
            'summary',
            'excerpt',
        ]);

        if ($metaDescription === '') {
            $metaDescription = $this->descriptionFromHtml($content);
        }

        $ogImage = $this->frontMatterValue($frontMatter, [
            'og:image',
            'og_image',
            'image',
            'cover',
            'cover_image',
        ]);
        $postImage = $this->frontMatterValue($frontMatter, [
            'image',
            'cover',
            'cover_image',
        ]);
        if ($postImage === '') {
            $postImage = $ogImage;
        }

        $postImageAlt = $this->frontMatterValue($frontMatter, [
            'image_alt',
            'image-alt',
            'imageAlt',
        ]);
        if ($postImageAlt === '') {
            $postImageAlt = $markdownTitle !== '' ? $markdownTitle : $siteTitle;
        }

        $markdownForRender = $markdown;
        if ($postImage !== '') {
            $markdownForRender = '![' . $this->escapeMarkdownAlt($postImageAlt) . '](' . $postImage . ')' . "\n\n" . ltrim($markdown);
        }

        $content = $this->parseMarkdown($markdownForRender);

        $ogTitle = $this->frontMatterValue($frontMatter, ['og:title', 'og_title']);
        if ($ogTitle === '') {
            $ogTitle = $pageTitle;
        }

        $ogDescription = $this->frontMatterValue($frontMatter, ['og:description', 'og_description']);
        if ($ogDescription === '') {
            $ogDescription = $metaDescription;
        }

        $metaAuthor = $this->frontMatterValue($frontMatter, ['author']);
        $metaKeywords = $this->frontMatterValue($frontMatter, ['keywords', 'tags']);
        $publishedTime = $this->frontMatterValue($frontMatter, ['publishDate', 'published', 'date']);

        $rendered = render_with_plugins([
            'type' => 'render_view',
            'view' => 'markdown.layout',
            'data' => [
                'content' => $content,
                'pageTitle' => $pageTitle,
                'metaDescription' => $this->nullableString($metaDescription),
                'ogTitle' => $ogTitle,
                'ogDescription' => $this->nullableString($ogDescription),
                'ogImage' => $this->nullableString($ogImage),
                'metaAuthor' => $this->nullableString($metaAuthor),
                'metaKeywords' => $this->nullableString($metaKeywords),
                'articlePublishedTime' => $this->nullableString($publishedTime),
            ],
        ]);

        return $rendered ?? $content;
    }

    private function parseMarkdown(string $markdown): string
    {
        $converter = new CommonMarkConverter();
        return $converter->convert($markdown)->getContent();
    }

    private function splitFrontMatter(string $source): array
    {
        $normalized = str_replace(["\r\n", "\r"], "\n", $source);

        if (!preg_match(self::FRONT_MATTER_PATTERN, $normalized, $matches)) {
            return [[], $source];
        }

        $frontMatter = $this->parseFrontMatterBlock($matches[1]);
        $markdown = substr($normalized, strlen($matches[0]));

        return [$frontMatter, $markdown];
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

    private function escapeMarkdownAlt(string $value): string
    {
        return str_replace([']', '['], ['\]', '\['], $value);
    }

    private function nullableString(string $value): ?string
    {
        $trimmed = trim($value);
        return $trimmed === '' ? null : $trimmed;
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
            $value = $normalized[strtolower($key)] ?? '';
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    private function descriptionFromHtml(string $html): string
    {
        $text = preg_replace('/\s+/', ' ', strip_tags($html));
        $text = trim($text ?? '');

        if ($text === '') {
            return '';
        }

        if (strlen($text) <= 160) {
            return $text;
        }

        return rtrim(substr($text, 0, 157)) . '...';
    }

    private function siteTitleFromConfig(): string
    {
        $configPath = __DIR__ . '/../config.php';
        if (!is_file($configPath)) {
            return '';
        }

        $config = require $configPath;
        if (!is_array($config)) {
            return '';
        }

        return trim((string) ($config['site_title'] ?? $config['site_name'] ?? ''));
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

    private function resolvePagePath(array $pageKeys, string $extension): ?string
    {
        $pagesPath = __DIR__ . '/../../views/pages';

        foreach ($pageKeys as $pageKey) {
            $candidate = $pagesPath . '/' . $pageKey . $extension;
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
