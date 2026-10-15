<?php

namespace App\Console\Commands;

use App\Content\ContentCompiler;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('content:clear')]
#[Description('Remove all compiled Markdown content')]
class ContentClear extends Command
{
    public function handle(ContentCompiler $compiler): int
    {
        $compiler->clear();

        $this->components->info('Compiled content cleared successfully.');

        return self::SUCCESS;
    }
}
