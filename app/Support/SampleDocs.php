<?php

namespace App\Support;

use Illuminate\Support\Str;

class SampleDocs
{
    /**
     * @var array<string, array<string, array<string, array<string, string>>>>
     */
    private const array NAVIGATION = [
        'laravel-l10n' => [
            '1.x' => [
                'getting started' => ['introduction' => 'Introduction', 'quickstart' => 'Quickstart'],
                'routing' => ['localized-routes' => 'Localized Routes', 'url-generation' => 'URL Generation'],
                'features' => [
                    'locale-preference' => 'Locale Preference',
                    'helpers' => 'Helpers',
                    'blade-components' => 'Blade Components',
                    'localized-views' => 'Localized Views',
                ],
                'frontend' => ['javascript' => 'JavaScript URL Generation'],
            ],
        ],
    ];

    /**
     * @return array<string, array<string, string>>
     */
    public static function navigation(string $package, string $version): array
    {
        return self::NAVIGATION[$package][$version] ?? [];
    }

    public static function view(string $package, string $version, string $page): string
    {
        return 'docs.samples.'.$package.'.v'.Str::before($version, '.').'.'.$page;
    }
}
