<?php

namespace App\Content\Markdown\Renderers;

use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

class CodeGroupRenderer implements NodeRendererInterface
{
    public function __construct(private FencedCodeRenderer $codeRenderer) {}

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        $blocks = [];

        foreach ($node->children() as $child) {
            if ($child instanceof FencedCode) {
                $blocks[] = $this->codeRenderer->highlight($child);
            }
        }

        return view('markdown.code-group', ['blocks' => $blocks])->render();
    }
}
