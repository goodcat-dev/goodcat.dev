<?php

namespace App\Content;

use App\Content\Markdown\MarkdownRenderer;
use Illuminate\Filesystem\Filesystem;

class ContentCompiler
{
    private ?string $version = null;

    /**
     * @param  list<string>  $dependencies
     */
    public function __construct(
        private Filesystem $files,
        private MarkdownRenderer $renderer,
        private string $cachePath,
        private array $dependencies = [],
    ) {}

    /**
     * @return array{front_matter: array<string, mixed>, html: string, sections: array<string, string>}
     */
    public function get(string $path, string $locale): array
    {
        if ($this->isExpired($path)) {
            $this->compile($path, $locale);
        }

        return $this->files->getRequire($this->compiledPath($path));
    }

    public function compile(string $path, string $locale): void
    {
        $translator = app('translator');
        $previous = $translator->getLocale();
        $translator->setLocale($locale);

        try {
            $compiled = $this->renderer->render($this->files->get($path));
        } finally {
            $translator->setLocale($previous);
        }

        $this->files->ensureDirectoryExists($this->cachePath);
        $this->files->replace($this->compiledPath($path), '<?php return '.var_export($compiled, true).';'.PHP_EOL);
    }

    public function clear(): void
    {
        $this->files->deleteDirectory($this->cachePath);
    }

    /**
     * @param  list<string>  $paths
     */
    public function prune(array $paths): void
    {
        if (! $this->files->isDirectory($this->cachePath)) {
            return;
        }

        $current = array_map(fn (string $path) => $this->compiledPath($path), $paths);

        foreach ($this->files->files($this->cachePath) as $file) {
            if (preg_match('/^[0-9a-f]{32}\.php$/', $file->getFilename()) && ! in_array($file->getPathname(), $current, true)) {
                $this->files->delete($file->getPathname());
            }
        }
    }

    private function isExpired(string $path): bool
    {
        $compiled = $this->compiledPath($path);

        return ! $this->files->exists($compiled) || $this->files->lastModified($compiled) < $this->files->lastModified($path);
    }

    private function compiledPath(string $path): string
    {
        return $this->cachePath.'/'.hash('xxh128', $path.'|'.$this->version()).'.php';
    }

    public function version(): string
    {
        return $this->version ??= hash('xxh128', collect($this->dependencies)
            ->flatMap(fn (string $path) => $this->files->isDirectory($path)
                ? array_map(fn ($file) => $file->getPathname(), $this->files->allFiles($path))
                : [$path])
            ->filter(fn (string $path) => $this->files->exists($path))
            ->sort()
            ->map(fn (string $path) => $path.':'.$this->files->lastModified($path))
            ->implode('|'));
    }
}
