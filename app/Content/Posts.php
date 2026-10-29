<?php

namespace App\Content;

use App\Models\Post;
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
        private string $markerPath,
    ) {}

    public function sync(): void
    {
        $entries = collect($this->entries());

        $entries->groupBy('key')->each(function (Collection $translations, string $key) {
            $attributes = $translations->mapWithKeys(fn (array $entry) => [$entry['locale'] => $this->attributes($entry['key'], $entry['locale'])]);
            $reference = $attributes[config('app.fallback_locale')] ?? $attributes->first();

            $post = Post::query()->firstOrNew(['key' => $key]);

            foreach ($post->getTranslatableAttributes() as $column) {
                $post->replaceTranslations($column, $attributes->map(fn (array $translation) => $translation[$column])->filter(fn ($value) => $value !== null)->all());
            }

            $post->fill([
                'package' => $reference['package'],
                'published_at' => $reference['published_at'],
            ])->save();

            $post->syncTags($reference['tags']);
        });

        Post::query()->whereNotIn('key', $entries->pluck('key')->unique())->get()->each->delete();

        $this->files->ensureDirectoryExists(dirname($this->markerPath));
        $this->files->replace($this->markerPath, json_encode(['version' => $this->compiler->version(), 'entries' => $entries->all()]));
    }

    public function isStale(): bool
    {
        if (! $this->files->exists($this->markerPath)) {
            return true;
        }

        $entries = $this->entries();

        if (json_decode($this->files->get($this->markerPath), true) !== ['version' => $this->compiler->version(), 'entries' => $entries]) {
            return true;
        }

        if (Post::query()->count() !== collect($entries)->pluck('key')->unique()->count()) {
            return true;
        }

        $syncedAt = $this->files->lastModified($this->markerPath);

        foreach ($entries as $entry) {
            if ($this->files->lastModified($this->path($entry['key'], $entry['locale'])) > $syncedAt) {
                return true;
            }
        }

        return false;
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
     * @return array{slug: string, headline: string, description: ?string, html: string, reading_time: int, package: ?string, published_at: Carbon, tags: list<string>}
     */
    private function attributes(string $key, string $locale): array
    {
        $compiled = $this->compiler->get($this->path($key, $locale), $locale);
        $frontMatter = $compiled['front_matter'];
        $publishedAt = $frontMatter['published_at'];

        return [
            'slug' => $frontMatter['slug'] ?? $key,
            'headline' => $frontMatter['title'] ?? $key,
            'description' => $frontMatter['description'] ?? null,
            'html' => $compiled['html'],
            'reading_time' => max(1, (int) ceil(str_word_count(strip_tags($compiled['html'])) / self::WORDS_PER_MINUTE)),
            'package' => $frontMatter['package'] ?? null,
            'published_at' => is_int($publishedAt) ? Carbon::createFromTimestamp($publishedAt) : Carbon::parse($publishedAt),
            'tags' => $frontMatter['tags'] ?? [],
        ];
    }
}
