<?php

namespace App\Content\Markdown\Listeners;

use App\Content\Markdown\Listeners\Concerns\WrapsDirectives;
use App\Content\Markdown\Nodes\Aside;
use League\CommonMark\Event\DocumentParsedEvent;

class ConvertAsides
{
    use WrapsDirectives;

    public function __invoke(DocumentParsedEvent $event): void
    {
        foreach ($this->openings($event->getDocument(), 'aside') as [$opening, $label]) {
            $this->wrap($opening, 'aside', new Aside($label));
        }
    }
}
