<?php

namespace App\Content\Search;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use RuntimeException;

class Algolia
{
    private const int WAIT_ATTEMPTS = 120;

    private const int WAIT_INTERVAL_MILLISECONDS = 500;

    public function __construct(
        private string $appId,
        private string $writeKey,
        private string $index,
    ) {}

    /**
     * @param  list<array<string, mixed>>  $records
     */
    public function replaceAll(array $records): void
    {
        $temporary = "{$this->index}_tmp";

        $this->wait($temporary, $this->client()->delete("/1/indexes/{$temporary}")->json('taskID'));
        $this->wait($temporary, $this->client()->put("/1/indexes/{$temporary}/settings", $this->settings())->throw()->json('taskID'));

        foreach (array_chunk($records, 1000) as $chunk) {
            $response = $this->client()->post("/1/indexes/{$temporary}/batch", [
                'requests' => array_map(fn (array $record) => ['action' => 'addObject', 'body' => $record], $chunk),
            ])->throw();

            $this->wait($temporary, $response->json('taskID'));
        }

        $move = $this->client()->post("/1/indexes/{$temporary}/operation", [
            'operation' => 'move',
            'destination' => $this->index,
        ])->throw();

        $this->wait($temporary, $move->json('taskID'));
    }

    /**
     * @param  list<array<string, mixed>>  $records
     */
    public function fingerprint(array $records): string
    {
        return hash('xxh128', json_encode([$this->index, $this->settings(), $records], JSON_THROW_ON_ERROR));
    }

    /**
     * @return array<string, mixed>
     */
    private function settings(): array
    {
        return [
            'searchableAttributes' => [
                'unordered(hierarchy.lvl1)',
                'unordered(hierarchy.lvl2)',
                'unordered(hierarchy.lvl3)',
                'content',
            ],
            'attributesForFaceting' => ['filterOnly(type)', 'filterOnly(package)', 'filterOnly(version)', 'filterOnly(lang)'],
            'attributesToHighlight' => ['hierarchy.lvl1', 'hierarchy.lvl2', 'hierarchy.lvl3'],
            'attributesToSnippet' => ['content:24'],
            'snippetEllipsisText' => '…',
            'customRanking' => ['asc(position)'],
            'indexLanguages' => ['en', 'it'],
            'queryLanguages' => ['en', 'it'],
            'ignorePlurals' => true,
            'removeStopWords' => true,
        ];
    }

    private function wait(string $index, ?int $taskId): void
    {
        if ($taskId === null) {
            return;
        }

        for ($attempt = 0; $attempt < self::WAIT_ATTEMPTS; $attempt++) {
            if ($this->client()->get("/1/indexes/{$index}/task/{$taskId}")->throw()->json('status') === 'published') {
                return;
            }

            Sleep::for(self::WAIT_INTERVAL_MILLISECONDS)->milliseconds();
        }

        $seconds = self::WAIT_ATTEMPTS * self::WAIT_INTERVAL_MILLISECONDS / 1000;

        throw new RuntimeException("Algolia task {$taskId} on {$index} was not published within {$seconds} seconds.");
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl("https://{$this->appId}.algolia.net")
            ->withHeaders([
                'X-Algolia-Application-Id' => $this->appId,
                'X-Algolia-API-Key' => $this->writeKey,
            ])
            ->acceptJson()
            ->timeout(30);
    }
}
