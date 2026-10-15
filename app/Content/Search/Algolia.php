<?php

namespace App\Content\Search;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;

class Algolia
{
    public function __construct(
        private string $appId,
        private string $adminKey,
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

        while ($this->client()->get("/1/indexes/{$index}/task/{$taskId}")->throw()->json('status') !== 'published') {
            Sleep::for(500)->milliseconds();
        }
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl("https://{$this->appId}.algolia.net")
            ->withHeaders([
                'X-Algolia-Application-Id' => $this->appId,
                'X-Algolia-API-Key' => $this->adminKey,
            ])
            ->acceptJson()
            ->timeout(30);
    }
}
