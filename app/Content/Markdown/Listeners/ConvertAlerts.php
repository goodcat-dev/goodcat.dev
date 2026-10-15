<?php

namespace App\Content\Markdown\Listeners;

use App\Content\Markdown\Nodes\Alert;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;

class ConvertAlerts
{
    public function __invoke(DocumentParsedEvent $event): void
    {
        $quotes = [];

        foreach ($event->getDocument()->iterator() as $node) {
            if ($node instanceof BlockQuote) {
                $quotes[] = $node;
            }
        }

        foreach ($quotes as $quote) {
            $paragraph = $quote->firstChild();

            if (! $paragraph instanceof Paragraph) {
                continue;
            }

            [$marker, $nodes] = $this->leadingText($paragraph);

            if (! preg_match('/^\[!(NOTE|WARNING)\]$/', trim($marker), $matches)) {
                continue;
            }

            foreach ($nodes as $node) {
                $node->detach();
            }

            $alert = new Alert(strtolower($matches[1]));

            foreach ($quote->children() as $child) {
                $alert->appendChild($child);
            }

            $quote->replaceWith($alert);
        }
    }

    /**
     * @return array{string, list<Node>}
     */
    private function leadingText(Paragraph $paragraph): array
    {
        $text = '';
        $nodes = [];

        foreach ($paragraph->children() as $child) {
            $nodes[] = $child;

            if ($child instanceof Newline) {
                break;
            }

            if (! $child instanceof Text) {
                return ['', []];
            }

            $text .= $child->getLiteral();
        }

        return [$text, $nodes];
    }
}
