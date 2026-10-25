<?php

namespace App\Content\Markdown\Renderers;

use App\Content\Markdown\Nodes\Tab;
use App\Content\Markdown\Nodes\Tabs;
use Illuminate\Support\Str;
use InvalidArgumentException;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

class TabsRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        if (! $node instanceof Tabs) {
            throw new InvalidArgumentException('Block must be an instance of '.Tabs::class);
        }

        $tabs = [];

        foreach ($node->children() as $child) {
            if ($child instanceof Tab) {
                $tabs[] = [
                    'id' => Str::slug($child->label),
                    'label' => $child->label,
                    'html' => $childRenderer->renderNodes($child->children()),
                ];
            }
        }

        return view('markdown.tabs', ['label' => $node->label, 'tabs' => $tabs])->render();
    }
}
