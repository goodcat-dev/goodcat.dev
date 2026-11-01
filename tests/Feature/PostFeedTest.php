<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_feed_lists_posts_of_its_language_newest_first(): void
    {
        Post::factory()->create([
            'slug' => ['en' => 'older-post', 'it' => 'articolo-vecchio'],
            'headline' => ['en' => 'Older `post`', 'it' => 'Articolo vecchio'],
            'description' => ['en' => 'An older post & more', 'it' => 'Un articolo vecchio'],
            'published_at' => '2026-01-10',
        ])->attachTag('laravel');
        Post::factory()->create([
            'slug' => ['en' => 'newer-post', 'it' => 'articolo-nuovo'],
            'headline' => ['en' => 'Newer post', 'it' => 'Articolo nuovo'],
            'published_at' => '2026-02-10',
        ]);
        Post::factory()->onlyIn('it')->create(['headline' => ['it' => 'Solo in italiano']]);

        $response = $this->get('/feed')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/atom+xml; charset=UTF-8');

        $feed = simplexml_load_string($response->getContent());

        $this->assertSame('en', (string) $feed->attributes('xml', true)->lang);
        $this->assertCount(2, $feed->entry);
        $this->assertSame('Newer post', (string) $feed->entry[0]->title);
        $this->assertSame('Older post', (string) $feed->entry[1]->title);
        $this->assertSame(url('/blog/older-post'), (string) $feed->entry[1]->link['href']);
        $this->assertSame('An older post & more', (string) $feed->entry[1]->summary);
        $this->assertSame('laravel', (string) $feed->entry[1]->category['term']);
        $this->assertSame(0, $feed->entry[0]->summary->count());
        $this->assertSame('2026-02-10T00:00:00+00:00', (string) $feed->updated);
    }

    public function test_italian_feed_links_to_translated_slugs(): void
    {
        Post::factory()->create(['slug' => ['en' => 'a-post', 'it' => 'un-articolo'], 'headline' => ['en' => 'A post', 'it' => 'Un articolo']]);

        $feed = simplexml_load_string($this->get('/it/feed')->assertOk()->getContent());

        $this->assertSame('it', (string) $feed->attributes('xml', true)->lang);
        $this->assertSame('Un articolo', (string) $feed->entry[0]->title);
        $this->assertSame(url('/it/blog/un-articolo'), (string) $feed->entry[0]->link['href']);
        $this->assertSame(url('/it/feed'), (string) $feed->id);
    }

    public function test_pages_advertise_the_feed_of_their_language(): void
    {
        $this->get('/')->assertSee('<link rel="alternate" type="application/atom+xml" title="goodcat.dev" href="'.url('/feed').'">', false);
        $this->get('/it')->assertSee('<link rel="alternate" type="application/atom+xml" title="goodcat.dev" href="'.url('/it/feed').'">', false);
    }
}
