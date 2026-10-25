<?php

namespace App\Content;

use Goodcat\L10n\Contracts\HasLocalizedRouteKey;
use Illuminate\Support\Carbon;

readonly class Post implements HasLocalizedRouteKey
{
    /**
     * @param  array<string, string>  $slugs
     * @param  list<string>  $tags
     * @param  array<string, string>  $sections
     */
    public function __construct(
        public string $key,
        public string $slug,
        public array $slugs,
        public string $title,
        public string $headline,
        public ?string $description,
        public Carbon $publishedAt,
        public ?string $package,
        public array $tags,
        public int $readingTime,
        public string $html,
        public array $sections,
    ) {}

    public function url(): string
    {
        return route('posts.show', $this);
    }

    public function getLocalizedRouteKey(string $locale, ?string $field = null): ?string
    {
        return $this->slugs[$locale] ?? null;
    }
}
