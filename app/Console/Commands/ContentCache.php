<?php

namespace App\Console\Commands;

use App\Content\ContentCompiler;
use App\Content\Docs;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('content:cache')]
#[Description('Compile all Markdown content into highlighted HTML')]
class ContentCache extends Command
{
    public function handle(ContentCompiler $compiler, Docs $docs): int
    {
        $compiler->clear();

        foreach ($docs->paths() as $path) {
            $compiler->compile($path);
        }

        $this->components->info('Content cached successfully.');

        return self::SUCCESS;
    }
}
