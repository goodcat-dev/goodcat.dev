<?php

namespace App\Content\Markdown\Renderers;

use App\Content\Markdown\Nodes\Aside;
use InvalidArgumentException;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

class AsideRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        if (! $node instanceof Aside) {
            throw new InvalidArgumentException('Block must be an instance of '.Aside::class);
        }

        return view('markdown.aside', [
            'label' => $node->label,
            'html' => $childRenderer->renderNodes($node->children()),
        ])->render();
    }
}
