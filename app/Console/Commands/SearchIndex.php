<?php

namespace App\Console\Commands;

use App\Content\Search\Algolia;
use App\Content\Search\DocsRecords;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('search:index')]
#[Description('Rebuild the Algolia search index from the Markdown content')]
class SearchIndex extends Command
{
    public function handle(DocsRecords $records): int
    {
        $config = config('services.algolia');

        if (blank($config['app_id']) || blank($config['admin_key'])) {
            $this->components->error('Algolia is not configured: set ALGOLIA_APP_ID and ALGOLIA_ADMIN_KEY.');

            return self::FAILURE;
        }

        $all = $records->all();

        (new Algolia($config['app_id'], $config['admin_key'], $config['index']))->replaceAll($all);

        $this->components->info(count($all).' records indexed in '.$config['index'].'.');

        return self::SUCCESS;
    }
}
