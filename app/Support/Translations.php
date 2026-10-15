<?php

namespace App\Support;

class Translations
{
    private const string MISSING = 'translations.missing';

    public function missing(string ...$locales): void
    {
        request()->attributes->set(self::MISSING, [...request()->attributes->get(self::MISSING, []), ...$locales]);
    }

    /**
     * @return array<string, array{label: string, url: ?string, current: bool}>
     */
    public function all(): array
    {
        $route = request()->route();

        if ($route === null || $route->getName() === null) {
            return [];
        }

        $missing = request()->attributes->get(self::MISSING, []);
        $locales = [];

        foreach (config('app.locales') as $locale => $label) {
            $locales[$locale] = [
                'label' => $label,
                'url' => in_array($locale, $missing, true)
                    ? null
                    : route($route->getName(), [...$route->parameters(), 'lang' => $locale]),
                'current' => $locale === app()->getLocale(),
            ];
        }

        return $locales;
    }
}
