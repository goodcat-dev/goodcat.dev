<?php

namespace App\Content\Markdown;

use App\Content\Markdown\Listeners\CollectSections;
use App\Content\Markdown\Listeners\ConvertAlerts;
use App\Content\Markdown\Listeners\ConvertAsides;
use App\Content\Markdown\Listeners\ConvertTabs;
use App\Content\Markdown\Listeners\GroupCodeBlocks;
use App\Content\Markdown\Nodes\Alert;
use App\Content\Markdown\Nodes\Aside;
use App\Content\Markdown\Nodes\CodeGroup;
use App\Content\Markdown\Nodes\Tabs;
use App\Content\Markdown\Renderers\AlertRenderer;
use App\Content\Markdown\Renderers\AsideRenderer;
use App\Content\Markdown\Renderers\CodeGroupRenderer;
use App\Content\Markdown\Renderers\FencedCodeRenderer;
use App\Content\Markdown\Renderers\TabsRenderer;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;
use Phiki\Phiki;

class MarkdownRenderer
{
    private MarkdownConverter $converter;

    public function __construct()
    {
        $environment = new Environment([
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
            'table' => ['wrap' => ['enabled' => true, 'tag' => 'div', 'attributes' => ['class' => 'table-wrap']]],
        ]);

        $phiki = (new Phiki)->theme('goodcat', resource_path('phiki/goodcat.json'));
        $codeRenderer = new FencedCodeRenderer($phiki, 'goodcat');

        $environment
            ->addExtension(new CommonMarkCoreExtension)
            ->addExtension(new TableExtension)
            ->addExtension(new FrontMatterExtension)
            ->addEventListener(DocumentParsedEvent::class, new GroupCodeBlocks)
            ->addEventListener(DocumentParsedEvent::class, new ConvertAlerts)
            ->addEventListener(DocumentParsedEvent::class, new ConvertTabs)
            ->addEventListener(DocumentParsedEvent::class, new ConvertAsides)
            ->addEventListener(DocumentParsedEvent::class, new CollectSections)
            ->addRenderer(FencedCode::class, $codeRenderer, 10)
            ->addRenderer(CodeGroup::class, new CodeGroupRenderer($codeRenderer))
            ->addRenderer(Alert::class, new AlertRenderer)
            ->addRenderer(Tabs::class, new TabsRenderer)
            ->addRenderer(Aside::class, new AsideRenderer);

        $this->converter = new MarkdownConverter($environment);
    }

    /**
     * @return array{front_matter: array<string, mixed>, html: string, sections: array<string, string>}
     */
    public function render(string $markdown): array
    {
        $result = $this->converter->convert($markdown);

        return [
            'front_matter' => $result instanceof RenderedContentWithFrontMatter ? (array) $result->getFrontMatter() : [],
            'html' => $result->getContent(),
            'sections' => $result->getDocument()->data->get('sections', []),
        ];
    }
}
