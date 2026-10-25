<?php

namespace App\Console\Commands;

use App\Content\ContentCompiler;
use App\Content\Docs;
use App\Content\Posts;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('content:cache')]
#[Description('Compile changed Markdown content into highlighted HTML and rebuild the posts index')]
class ContentCache extends Command
{
    public function handle(ContentCompiler $compiler, Docs $docs, Posts $posts): int
    {
        foreach ($docs->all() as $entry) {
            $compiler->get($docs->path($entry['package'], $entry['version'], $entry['page'], $entry['locale']), $entry['locale']);
        }

        $posts->buildIndex();

        $this->components->info('Content cached successfully.');

        return self::SUCCESS;
    }
}
