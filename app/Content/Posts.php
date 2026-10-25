<?php

namespace App\Content;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Posts
{
    private const int WORDS_PER_MINUTE = 200;

    /** @var array<string, Collection<int, Post>> */
    private array $posts = [];

    /** @var ?list<array{key: string, locale: string, slug: string, slugs: array<string, string>, headline: string, description: ?string, published_at: int, package: ?string, tags: list<string>, reading_time: int}> */
    private ?array $index = null;

    public function __construct(
        private Filesystem $files,
        private ContentCompiler $compiler,
        private string $basePath,
        private string $indexPath,
        private bool $checksFreshness = true,
    ) {}

    /**
     * @return Collection<int, Post>
     */
    public function all(string $locale = 'en'): Collection
    {
        return $this->posts[$locale] ??= collect($this->index())
            ->where('locale', $locale)
            ->map(fn (array $entry) => new Post(
                key: $entry['key'],
                slug: $entry['slug'],
                slugs: $entry['slugs'],
                title: str_replace('`', '', $entry['headline']),
                headline: $entry['headline'],
                description: $entry['description'],
                publishedAt: Carbon::createFromTimestamp($entry['published_at']),
                package: $entry['package'],
                tags: $entry['tags'],
                readingTime: $entry['reading_time'],
            ))
            ->sortByDesc(fn (Post $post) => $post->publishedAt)
            ->values();
    }

    public function find(string $key, string $locale = 'en'): ?Post
    {
        return $this->all($locale)->first(fn (Post $post) => $post->key === $key);
    }

    public function findBySlug(string $slug, string $locale = 'en'): ?Post
    {
        return $this->all($locale)->first(fn (Post $post) => $post->slug === $slug);
    }

    public function html(Post $post, string $locale): string
    {
        return $this->compiler->get($this->path($post->key, $locale), $locale)['html'];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function buildIndex(): array
    {
        $entries = collect($this->entries())->map(function (array $entry) {
            $compiled = $this->compiler->get($this->path($entry['key'], $entry['locale']), $entry['locale']);
            $frontMatter = $compiled['front_matter'];
            $publishedAt = $frontMatter['published_at'];

            return [
                ...$entry,
                'slug' => $frontMatter['slug'] ?? $entry['key'],
                'headline' => $frontMatter['title'] ?? $entry['key'],
                'description' => $frontMatter['description'] ?? null,
                'published_at' => is_int($publishedAt) ? $publishedAt : Carbon::parse($publishedAt)->getTimestamp(),
                'package' => $frontMatter['package'] ?? null,
                'tags' => $frontMatter['tags'] ?? [],
                'reading_time' => max(1, (int) ceil(str_word_count(strip_tags($compiled['html'])) / self::WORDS_PER_MINUTE)),
            ];
        });

        $slugs = $entries->groupBy('key')->map(fn (Collection $translations) => $translations->pluck('slug', 'locale')->all());

        $index = $entries->map(fn (array $entry) => [...$entry, 'slugs' => $slugs[$entry['key']]])->all();

        $this->files->ensureDirectoryExists(dirname($this->indexPath));
        $this->files->replace($this->indexPath, '<?php return '.var_export($index, true).';'.PHP_EOL);

        $this->posts = [];

        return $this->index = $index;
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
            ->sortBy(['locale', 'key'])
            ->values()
            ->all();
    }

    public function path(string $key, string $locale): string
    {
        return "{$this->basePath}/{$locale}/{$key}.md";
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function index(): array
    {
        if ($this->index !== null) {
            return $this->index;
        }

        if (! $this->files->exists($this->indexPath)) {
            return $this->buildIndex();
        }

        $index = $this->files->getRequire($this->indexPath);

        if ($this->checksFreshness && $this->isStale($index)) {
            return $this->buildIndex();
        }

        return $this->index = $index;
    }

    /**
     * @param  list<array<string, mixed>>  $index
     */
    private function isStale(array $index): bool
    {
        $entries = $this->entries();
        $builtAt = $this->files->lastModified($this->indexPath);

        if (array_map(fn (array $entry) => [$entry['key'], $entry['locale']], $index) !== array_map(fn (array $entry) => [$entry['key'], $entry['locale']], $entries)) {
            return true;
        }

        foreach ($entries as $entry) {
            if ($this->files->lastModified($this->path($entry['key'], $entry['locale'])) > $builtAt) {
                return true;
            }
        }

        return false;
    }
}
