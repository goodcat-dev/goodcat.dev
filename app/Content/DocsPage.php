<?php

namespace App\Content;

readonly class DocsPage
{
    /**
     * @param  array<string, string>  $sections
     * @param  array<string, mixed>  $frontMatter
     */
    public function __construct(
        public string $slug,
        public string $title,
        public ?string $description,
        public string $html,
        public array $sections,
        public array $frontMatter,
    ) {}
}
