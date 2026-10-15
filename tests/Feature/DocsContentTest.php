<?php

namespace Tests\Feature;

use App\Content\Docs;
use DOMDocument;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DocsContentTest extends TestCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public static function versions(): array
    {
        $versions = [];

        foreach (glob(dirname(__DIR__, 2).'/content/docs/*/*', GLOB_ONLYDIR) as $directory) {
            $versions[basename(dirname($directory)).' '.basename($directory)] = [basename(dirname($directory)), basename($directory)];
        }

        return $versions;
    }

    #[DataProvider('versions')]
    public function test_every_locale_has_the_same_pages(string $package, string $version): void
    {
        $docs = app(Docs::class);
        $reference = config('app.fallback_locale');
        $pages = fn (string $locale) => collect(File::files(base_path("content/docs/{$package}/{$version}/{$locale}")))
            ->filter(fn ($file) => $file->getExtension() === 'md')
            ->map(fn ($file) => $file->getFilenameWithoutExtension())
            ->sort()
            ->values()
            ->all();

        foreach (array_keys(config('app.locales')) as $locale) {
            $this->assertSame($pages($reference), $pages($locale), "Pages in {$locale} differ from {$reference}.");
            $this->assertSame(
                array_keys(Arr::collapse($docs->navigation($package, $version, $reference))),
                array_keys(Arr::collapse($docs->navigation($package, $version, $locale))),
                "Navigation in {$locale} differs from {$reference}.",
            );
        }
    }

    #[DataProvider('versions')]
    public function test_internal_links_point_to_existing_pages_and_headings(string $package, string $version): void
    {
        $docs = app(Docs::class);

        foreach (array_keys(config('app.locales')) as $locale) {
            foreach (array_keys(Arr::collapse($docs->navigation($package, $version, $locale))) as $slug) {
                $page = $docs->find($package, $version, $slug, $locale);

                foreach ($this->relativeLinks($page->html) as $link) {
                    [$target, $anchor] = array_pad(explode('#', $link, 2), 2, null);
                    $targetPage = $target === '' ? $page : $docs->find($package, $version, $target, $locale);

                    $this->assertNotNull($targetPage, "{$locale}/{$slug}: link to missing page \"{$link}\".");

                    if ($anchor !== null) {
                        $this->assertStringContainsString("id=\"{$anchor}\"", $targetPage->html, "{$locale}/{$slug}: link to missing heading \"{$link}\".");
                    }
                }
            }
        }
    }

    /**
     * @return list<string>
     */
    private function relativeLinks(string $html): array
    {
        $document = new DOMDocument;
        $document->loadHTML('<meta charset="utf-8">'.$html, LIBXML_NOERROR | LIBXML_NOWARNING);
        $links = [];

        foreach ($document->getElementsByTagName('a') as $anchor) {
            $href = $anchor->getAttribute('href');

            if ($href !== '' && ! preg_match('#^([a-z]+:|/)#i', $href)) {
                $links[] = $href;
            }
        }

        return $links;
    }
}
