<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SamplePosts
{
    /**
     * @return Collection<int, array{slug: string, title: string, package: ?string, tags: list<string>, published_at: Carbon, reading_time: int, url: string}>
     */
    public static function all(): Collection
    {
        return collect([
            [
                'slug' => 'localized-routes-in-vue',
                'title' => 'Localized routes in Vue without passing lang every time',
                'package' => 'laravel-l10n',
                'tags' => ['vue', 'inertia', 'ssr'],
                'published_at' => '2026-10-02',
                'reading_time' => 6,
            ],
            [
                'slug' => 'translated-urls-without-a-second-router',
                'title' => 'Translated URLs without a second router',
                'package' => 'laravel-l10n',
                'tags' => [],
                'published_at' => '2026-09-28',
                'reading_time' => 7,
            ],
            [
                'slug' => 'validating-a-million-rows',
                'title' => 'Validating a million rows without Validator::make()',
                'package' => null,
                'tags' => [],
                'published_at' => '2026-09-12',
                'reading_time' => 9,
            ],
            [
                'slug' => 'extracting-a-package-from-a-client-project',
                'title' => 'Extracting a package from a client project',
                'package' => null,
                'tags' => [],
                'published_at' => '2026-08-30',
                'reading_time' => 6,
            ],
            [
                'slug' => 'query-string-filters-as-php-attributes',
                'title' => 'Query string filters as PHP attributes',
                'package' => 'laravel-filter-querystring',
                'tags' => [],
                'published_at' => '2026-08-14',
                'reading_time' => 5,
            ],
        ])->map(fn (array $post) => [
            ...$post,
            'published_at' => Carbon::parse($post['published_at']),
            'url' => route('posts.show', $post['slug']),
        ]);
    }
}
