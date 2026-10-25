<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

#[Signature('cdn:purge')]
#[Description('Purge every cached page from the Cloudflare CDN')]
class CdnPurge extends Command
{
    public function handle(): int
    {
        $config = config('services.cloudflare');

        if (blank($config['zone_id']) || blank($config['api_token'])) {
            $this->components->warn('Cloudflare is not configured: set CLOUDFLARE_ZONE_ID and CLOUDFLARE_API_TOKEN. Skipping.');

            return self::SUCCESS;
        }

        $response = Http::withToken($config['api_token'])
            ->post("https://api.cloudflare.com/client/v4/zones/{$config['zone_id']}/purge_cache", ['purge_everything' => true]);

        if (! $response->successful() || ! $response->json('success')) {
            $this->components->error('Cloudflare purge failed: '.($response->json('errors.0.message') ?? $response->status()));

            return self::FAILURE;
        }

        $this->components->info('Cloudflare cache purged.');

        return self::SUCCESS;
    }
}
