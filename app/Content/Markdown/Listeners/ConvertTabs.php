<?php

namespace App\Content\Markdown\Listeners;

use App\Content\Markdown\Listeners\Concerns\WrapsDirectives;
use App\Content\Markdown\Nodes\Tab;
use App\Content\Markdown\Nodes\Tabs;
use League\CommonMark\Event\DocumentParsedEvent;

class ConvertTabs
{
    use WrapsDirectives;

    public function __invoke(DocumentParsedEvent $event): void
    {
        foreach ($this->openings($event->getDocument(), 'tabs') as [$opening, $label]) {
            $tabs = new Tabs($label);
            $this->wrap($opening, 'tabs', $tabs);

            $tab = null;

            foreach ($tabs->children() as $child) {
                [$directive, $argument] = $this->directive($child);

                if ($directive === 'tab') {
                    $tab = new Tab($argument);
                    $child->replaceWith($tab);
                } elseif ($tab !== null) {
                    $tab->appendChild($child);
                }
            }
        }
    }
}
