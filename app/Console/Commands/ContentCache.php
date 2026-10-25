<?php

namespace App\Console\Commands;

use App\Content\ContentCompiler;
use App\Content\Docs;
use App\Content\Posts;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('content:cache')]
#[Description('Compile changed Markdown content into highlighted HTML, rebuild the posts index and remove outdated files')]
class ContentCache extends Command
{
    public function handle(ContentCompiler $compiler, Docs $docs, Posts $posts): int
    {
        $paths = [];

        foreach ($docs->all() as $entry) {
            $paths[] = $path = $docs->path($entry['package'], $entry['version'], $entry['page'], $entry['locale']);
            $compiler->get($path, $entry['locale']);
        }

        $posts->buildIndex();

        foreach ($posts->entries() as $entry) {
            $paths[] = $posts->path($entry['key'], $entry['locale']);
        }

        $compiler->prune($paths);

        $this->components->info('Content cached successfully.');

        return self::SUCCESS;
    }
}
