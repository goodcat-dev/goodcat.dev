<?php

namespace Tests\Feature;

use App\Content\Markdown\MarkdownRenderer;
use Tests\TestCase;

class MarkdownDirectivesTest extends TestCase
{
    public function test_tabs_split_their_content_into_panels(): void
    {
        $html = app(MarkdownRenderer::class)->render(<<<'MD'
            <!-- tabs: Pick your stack -->
            <!-- tab: Ziggy -->
            ## Ziggy

            Ziggy content.
            <!-- tab: Wayfinder -->
            ## Wayfinder

            Wayfinder content.
            <!-- /tabs -->

            After the tabs.
            MD)['html'];

        $this->assertStringContainsString('<span id="tabs-pick-your-stack" class="text-base font-bold">Pick your stack</span>', $html);
        $this->assertStringContainsString('x-data="tabs(\'ziggy\')"', $html);
        $this->assertMatchesRegularExpression('/<section id="panel-ziggy"[^>]*>\s*<h2 id="ziggy">Ziggy<\/h2>\s*<p>Ziggy content.<\/p>\s*<\/section>/', $html);
        $this->assertMatchesRegularExpression('/<section id="panel-wayfinder"[^>]*x-cloak[^>]*>\s*<h2 id="wayfinder">Wayfinder<\/h2>/', $html);
        $this->assertStringNotContainsString('<!--', $html);
        $this->assertStringEndsWith("<p>After the tabs.</p>\n", $html);
    }

    public function test_asides_wrap_their_content_under_a_label(): void
    {
        $html = app(MarkdownRenderer::class)->render(<<<'MD'
            <!-- aside: before you start -->
            Share the locale.

            ```php
            echo app()->getLocale();
            ```
            <!-- /aside -->
            MD)['html'];

        $this->assertStringStartsWith('<aside', $html);
        $this->assertMatchesRegularExpression('/\/\/ before you start<\/span>\s*<p>Share the locale.<\/p>\s*<figure/', $html);
        $this->assertStringEndsWith('</aside>', rtrim($html));
        $this->assertStringContainsString('class="phiki', $html);
        $this->assertStringNotContainsString('<!--', $html);
    }

    public function test_headings_inside_tabs_are_collected_as_sections(): void
    {
        $sections = app(MarkdownRenderer::class)->render(<<<'MD'
            <!-- tabs: Stack -->
            <!-- tab: Ziggy -->
            ## Ziggy
            <!-- tab: Wayfinder -->
            ## Wayfinder
            <!-- /tabs -->
            MD)['sections'];

        $this->assertSame(['ziggy' => 'Ziggy', 'wayfinder' => 'Wayfinder'], $sections);
    }
}
