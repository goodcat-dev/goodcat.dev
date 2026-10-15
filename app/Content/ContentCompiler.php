<?php

namespace App\Content;

use App\Content\Markdown\MarkdownRenderer;
use Illuminate\Filesystem\Filesystem;

class ContentCompiler
{
    public function __construct(
        private Filesystem $files,
        private MarkdownRenderer $renderer,
        private string $cachePath,
    ) {}

    /**
     * @return array{front_matter: array<string, mixed>, html: string, sections: array<string, string>}
     */
    public function get(string $path): array
    {
        if ($this->isExpired($path)) {
            $this->compile($path);
        }

        return $this->files->getRequire($this->compiledPath($path));
    }

    public function compile(string $path): void
    {
        $compiled = $this->renderer->render($this->files->get($path));

        $this->files->ensureDirectoryExists($this->cachePath);
        $this->files->replace($this->compiledPath($path), '<?php return '.var_export($compiled, true).';'.PHP_EOL);
    }

    public function clear(): void
    {
        $this->files->deleteDirectory($this->cachePath);
    }

    private function isExpired(string $path): bool
    {
        $compiled = $this->compiledPath($path);

        return ! $this->files->exists($compiled) || $this->files->lastModified($compiled) < $this->files->lastModified($path);
    }

    private function compiledPath(string $path): string
    {
        return $this->cachePath.'/'.hash('xxh128', $path).'.php';
    }
}
