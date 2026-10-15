<?php

namespace App\Content;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class Docs
{
    public function __construct(
        private Filesystem $files,
        private ContentCompiler $compiler,
        private string $basePath,
    ) {}

    public function find(string $package, string $version, string $page, string $locale = 'en'): ?DocsPage
    {
        $path = $this->directory($package, $version, $locale)."/{$page}.md";

        if (! $this->files->exists($path)) {
            return null;
        }

        $compiled = $this->compiler->get($path);

        return new DocsPage(
            slug: $page,
            title: $compiled['front_matter']['title'] ?? $page,
            description: $compiled['front_matter']['description'] ?? null,
            html: $compiled['html'],
            sections: $compiled['sections'],
            frontMatter: $compiled['front_matter'],
        );
    }

    public function exists(string $package, string $version, string $locale = 'en'): bool
    {
        return $this->files->isDirectory($this->directory($package, $version, $locale));
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function navigation(string $package, string $version, string $locale = 'en'): array
    {
        $path = $this->directory($package, $version, $locale).'/navigation.yaml';

        return $this->files->exists($path) ? Yaml::parseFile($path) : [];
    }

    /**
     * @return list<string>
     */
    public function paths(): array
    {
        if (! $this->files->isDirectory($this->basePath)) {
            return [];
        }

        return collect($this->files->allFiles($this->basePath))
            ->filter(fn ($file) => $file->getExtension() === 'md')
            ->map(fn ($file) => $file->getPathname())
            ->values()
            ->all();
    }

    private function directory(string $package, string $version, string $locale): string
    {
        return "{$this->basePath}/{$package}/{$version}/{$locale}";
    }
}
