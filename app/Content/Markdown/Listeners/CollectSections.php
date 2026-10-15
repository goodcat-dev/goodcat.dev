<?php

namespace App\Content\Markdown\Listeners;

use Illuminate\Support\Str;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\StringContainerHelper;

class CollectSections
{
    public function __invoke(DocumentParsedEvent $event): void
    {
        $document = $event->getDocument();
        $sections = [];

        foreach ($document->iterator() as $node) {
            if (! $node instanceof Heading || $node->getLevel() > 3) {
                continue;
            }

            $title = StringContainerHelper::getChildText($node);
            $id = Str::slug($title);

            $node->data->set('attributes/id', $id);

            if ($node->getLevel() === 2) {
                $sections[$id] = $title;
            }
        }

        $document->data->set('sections', $sections);
    }
}
