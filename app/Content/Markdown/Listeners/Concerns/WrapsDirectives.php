<?php

namespace App\Content\Markdown\Listeners\Concerns;

use League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Node;

trait WrapsDirectives
{
    /**
     * @return list<array{HtmlBlock, ?string}>
     */
    private function openings(Document $document, string $name): array
    {
        $openings = [];

        foreach ($document->iterator() as $node) {
            [$directive, $argument] = $this->directive($node);

            if ($directive === $name) {
                $openings[] = [$node, $argument];
            }
        }

        return $openings;
    }

    private function wrap(HtmlBlock $opening, string $name, AbstractBlock $container): void
    {
        $sibling = $opening->next();

        while ($sibling !== null && $this->directive($sibling)[0] !== "/{$name}") {
            $next = $sibling->next();
            $container->appendChild($sibling);
            $sibling = $next;
        }

        $sibling?->detach();
        $opening->replaceWith($container);
    }

    /**
     * @return array{?string, ?string}
     */
    private function directive(Node $node): array
    {
        if (! $node instanceof HtmlBlock || ! preg_match('/^<!--\s*(\/?[a-z-]+)(?::\s*(.+?))?\s*-->$/', trim($node->getLiteral()), $matches)) {
            return [null, null];
        }

        return [$matches[1], $matches[2] ?? null];
    }
}
