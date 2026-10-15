<?php

namespace App\Content\Search;

use App\Content\Docs;
use App\Content\DocsPage;
use DOMDocument;
use DOMElement;
use DOMNode;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DocsRecords
{
    private const int CONTENT_LIMIT = 4000;

    public function __construct(private Docs $docs) {}

    /**
     * @return list<array<string, mixed>>
     */
    public function all(): array
    {
        $records = [];

        foreach ($this->docs->all() as $entry) {
            $page = $this->docs->find($entry['package'], $entry['version'], $entry['page'], $entry['locale']);
            $navigation = $this->docs->navigation($entry['package'], $entry['version'], $entry['locale']);
            $group = collect($navigation)->search(fn (array $pages) => array_key_exists($entry['page'], $pages)) ?: null;

            array_push($records, ...$this->forPage($entry['package'], $entry['version'], $entry['locale'], $page, $group));
        }

        return $records;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function forPage(string $package, string $version, string $locale, DocsPage $page, ?string $group = null): array
    {
        $sections = [['lvl2' => null, 'lvl3' => null, 'anchor' => null, 'content' => $page->description ?? '']];

        foreach ($this->topLevelNodes($page->html) as $node) {
            if ($node instanceof DOMElement && in_array($node->tagName, ['h2', 'h3'], true)) {
                $sections[] = [
                    'lvl2' => $node->tagName === 'h2' ? $this->text($node) : Arr::last($sections)['lvl2'],
                    'lvl3' => $node->tagName === 'h3' ? $this->text($node) : null,
                    'anchor' => $node->getAttribute('id') ?: null,
                    'content' => '',
                ];

                continue;
            }

            if ($node instanceof DOMElement && $node->hasAttribute('data-code')) {
                continue;
            }

            $sections[array_key_last($sections)]['content'] .= ' '.$this->text($node);
        }

        $path = route('docs.show', [$package, $version, $page->slug, 'lang' => $locale], absolute: false);

        return collect($sections)->map(fn (array $section, int $position) => [
            'objectID' => "docs:{$package}:{$version}:{$locale}:{$page->slug}#".($section['anchor'] ?? ''),
            'type' => 'docs',
            'package' => $package,
            'version' => $version,
            'lang' => $locale,
            'url' => $path.($section['anchor'] ? "#{$section['anchor']}" : ''),
            'hierarchy' => [
                'lvl0' => $group,
                'lvl1' => $page->title,
                'lvl2' => $section['lvl2'],
                'lvl3' => $section['lvl3'],
            ],
            'content' => Str::limit(Str::squish($section['content']), self::CONTENT_LIMIT),
            'position' => $position,
        ])->values()->all();
    }

    /**
     * @return iterable<DOMNode>
     */
    private function topLevelNodes(string $html): iterable
    {
        $document = new DOMDocument;
        $document->loadHTML('<meta charset="utf-8"><div id="root">'.$html.'</div>', LIBXML_NOERROR | LIBXML_NOWARNING);

        return $document->getElementById('root')->childNodes;
    }

    private function text(DOMNode $node): string
    {
        return Str::squish($node->textContent);
    }
}
