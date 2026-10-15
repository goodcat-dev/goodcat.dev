<?php

namespace App\Content\Markdown\Renderers;

use App\Content\Markdown\Nodes\Alert;
use InvalidArgumentException;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

class AlertRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        if (! $node instanceof Alert) {
            throw new InvalidArgumentException('Block must be an instance of '.Alert::class);
        }

        return view('markdown.alert', [
            'type' => $node->type,
            'html' => $childRenderer->renderNodes($node->children()),
        ])->render();
    }
}
