<?php

namespace App\Console\Commands;

use App\Content\ContentCompiler;
use App\Content\Docs;
use App\Content\Posts;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('content:cache')]
#[Description('Compile all Markdown content into highlighted HTML')]
class ContentCache extends Command
{
    public function handle(ContentCompiler $compiler, Docs $docs, Posts $posts): int
    {
        $compiler->clear();

        foreach ($docs->all() as $entry) {
            $compiler->compile($docs->path($entry['package'], $entry['version'], $entry['page'], $entry['locale']), $entry['locale']);
        }

        foreach ($posts->entries() as $entry) {
            $compiler->compile($posts->path($entry['key'], $entry['locale']), $entry['locale']);
        }

        $this->components->info('Content cached successfully.');

        return self::SUCCESS;
    }
}
