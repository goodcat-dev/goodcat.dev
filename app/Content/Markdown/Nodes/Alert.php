<?php

namespace App\Content\Markdown\Nodes;

use League\CommonMark\Node\Block\AbstractBlock;

class Alert extends AbstractBlock
{
    public function __construct(public readonly string $type)
    {
        parent::__construct();
    }
}
