<?php

namespace App\Content\Markdown\Renderers;

use InvalidArgumentException;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use Phiki\Grammar\Grammar;
use Phiki\Phiki;

class FencedCodeRenderer implements NodeRendererInterface
{
    public function __construct(private Phiki $phiki, private string $theme) {}

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        return view('markdown.code-block', $this->highlight($node))->render();
    }

    /**
     * @return array{title: ?string, language: ?string, html: string}
     */
    public function highlight(Node $node): array
    {
        if (! $node instanceof FencedCode) {
            throw new InvalidArgumentException('Block must be an instance of '.FencedCode::class);
        }

        $language = $node->getInfoWords()[0] ?? '';
        preg_match('/title="([^"]*)"/', $node->getInfo() ?? '', $title);

        $grammar = $language !== '' && $this->phiki->environment->grammars->has($language) ? $language : Grammar::Txt;

        return [
            'title' => $title[1] ?? null,
            'language' => $language !== '' ? $language : null,
            'html' => $this->phiki->codeToHtml(rtrim($node->getLiteral(), "\n"), $grammar, $this->theme)->toString(),
        ];
    }
}
