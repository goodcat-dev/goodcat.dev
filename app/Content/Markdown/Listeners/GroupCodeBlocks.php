<?php

namespace App\Content\Markdown\Listeners;

use App\Content\Markdown\Listeners\Concerns\WrapsDirectives;
use App\Content\Markdown\Nodes\CodeGroup;
use League\CommonMark\Event\DocumentParsedEvent;

class GroupCodeBlocks
{
    use WrapsDirectives;

    public function __invoke(DocumentParsedEvent $event): void
    {
        foreach ($this->openings($event->getDocument(), 'code-group') as [$opening]) {
            $this->wrap($opening, 'code-group', new CodeGroup);
        }
    }
}
