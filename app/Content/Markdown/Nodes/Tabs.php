<?php

namespace App\Content\Markdown\Nodes;

use League\CommonMark\Node\Block\AbstractBlock;

class Tabs extends AbstractBlock
{
    public function __construct(public readonly ?string $label)
    {
        parent::__construct();
    }
}
