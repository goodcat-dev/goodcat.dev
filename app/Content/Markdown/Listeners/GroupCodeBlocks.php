<?php

namespace App\Content\Markdown\Listeners;

use App\Content\Markdown\Nodes\CodeGroup;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock;

class GroupCodeBlocks
{
    private const string OPEN = '<!-- code-group -->';

    private const string CLOSE = '<!-- /code-group -->';

    public function __invoke(DocumentParsedEvent $event): void
    {
        $openings = [];

        foreach ($event->getDocument()->iterator() as $node) {
            if ($node instanceof HtmlBlock && trim($node->getLiteral()) === self::OPEN) {
                $openings[] = $node;
            }
        }

        foreach ($openings as $opening) {
            $group = new CodeGroup;
            $sibling = $opening->next();

            while ($sibling !== null && ! ($sibling instanceof HtmlBlock && trim($sibling->getLiteral()) === self::CLOSE)) {
                $next = $sibling->next();
                $group->appendChild($sibling);
                $sibling = $next;
            }

            $sibling?->detach();
            $opening->replaceWith($group);
        }
    }
}
