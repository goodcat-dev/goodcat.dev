<?php

namespace App\Console\Commands;

use App\Content\Search\Algolia;
use App\Content\Search\DocsRecords;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Client\HttpClientException;
use RuntimeException;

#[Signature('search:index {--force : Rebuild the index even if the content has not changed}')]
#[Description('Rebuild the Algolia search index from the Markdown content')]
class SearchIndex extends Command
{
    public function handle(DocsRecords $records, Filesystem $files): int
    {
        $config = config('services.algolia');

        if (blank($config['app_id']) || blank($config['write_key'])) {
            $this->components->warn('Algolia is not configured: set ALGOLIA_APP_ID and ALGOLIA_WRITE_KEY. Skipping.');

            return self::SUCCESS;
        }

        $algolia = new Algolia($config['app_id'], $config['write_key'], $config['index']);
        $all = $records->all();
        $fingerprint = $algolia->fingerprint($all);
        $fingerprintPath = storage_path("framework/search/{$config['index']}.hash");

        if (! $this->option('force') && $files->exists($fingerprintPath) && $files->get($fingerprintPath) === $fingerprint) {
            $this->components->info("The content has not changed since the last indexing: {$config['index']} is up to date.");

            return self::SUCCESS;
        }

        try {
            $algolia->replaceAll($all);
        } catch (HttpClientException|RuntimeException $exception) {
            $this->components->error('Algolia indexing failed: '.$exception->getMessage());

            return self::FAILURE;
        }

        $files->ensureDirectoryExists(dirname($fingerprintPath));
        $files->put($fingerprintPath, $fingerprint);

        $this->components->info(count($all).' records indexed in '.$config['index'].'.');

        return self::SUCCESS;
    }
}
