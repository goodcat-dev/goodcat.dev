<?php

namespace App\Content;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Posts
{
    private const int WORDS_PER_MINUTE = 200;

    public function __construct(
        private Filesystem $files,
        private ContentCompiler $compiler,
        private string $basePath,
    ) {}

    /**
     * @return Collection<int, Post>
     */
    public function all(string $locale = 'en'): Collection
    {
        return collect($this->entries())
            ->where('locale', $locale)
            ->map(fn (array $entry) => $this->find($entry['key'], $locale))
            ->sortByDesc(fn (Post $post) => $post->publishedAt)
            ->values();
    }

    public function findBySlug(string $slug, string $locale = 'en'): ?Post
    {
        return $this->all($locale)->first(fn (Post $post) => $post->slug === $slug);
    }

    public function find(string $key, string $locale = 'en'): ?Post
    {
        $path = $this->path($key, $locale);

        if (! $this->files->exists($path)) {
            return null;
        }

        $compiled = $this->compiler->get($path, $locale);
        $frontMatter = $compiled['front_matter'];
        $headline = $frontMatter['title'] ?? $key;
        $publishedAt = $frontMatter['published_at'];

        return new Post(
            key: $key,
            slug: $frontMatter['slug'] ?? $key,
            slugs: $this->slugs($key),
            title: str_replace('`', '', $headline),
            headline: $headline,
            description: $frontMatter['description'] ?? null,
            publishedAt: is_int($publishedAt) ? Carbon::createFromTimestamp($publishedAt) : Carbon::parse($publishedAt),
            package: $frontMatter['package'] ?? null,
            tags: $frontMatter['tags'] ?? [],
            readingTime: max(1, (int) ceil(str_word_count(strip_tags($compiled['html'])) / self::WORDS_PER_MINUTE)),
            html: $compiled['html'],
            sections: $compiled['sections'],
        );
    }

    /**
     * @return array<string, string>
     */
    private function slugs(string $key): array
    {
        return collect($this->entries())
            ->where('key', $key)
            ->mapWithKeys(fn (array $entry) => [
                $entry['locale'] => $this->compiler->get($this->path($key, $entry['locale']), $entry['locale'])['front_matter']['slug'] ?? $key,
            ])
            ->all();
    }

    /**
     * @return list<array{key: string, locale: string}>
     */
    public function entries(): array
    {
        if (! $this->files->isDirectory($this->basePath)) {
            return [];
        }

        return collect($this->files->allFiles($this->basePath))
            ->filter(fn ($file) => $file->getExtension() === 'md' && $file->getRelativePath() !== '')
            ->map(fn ($file) => ['key' => $file->getFilenameWithoutExtension(), 'locale' => $file->getRelativePath()])
            ->values()
            ->all();
    }

    public function path(string $key, string $locale): string
    {
        return "{$this->basePath}/{$locale}/{$key}.md";
    }
}
