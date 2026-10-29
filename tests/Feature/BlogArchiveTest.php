<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogArchiveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Post::factory()
            ->count(25)
            ->sequence(fn (Sequence $sequence) => [
                'key' => 'post-'.($sequence->index + 1),
                'slug' => ['en' => 'post-'.($sequence->index + 1), 'it' => 'articolo-'.($sequence->index + 1)],
                'headline' => ['en' => 'Post '.($sequence->index + 1), 'it' => 'Articolo '.($sequence->index + 1)],
                'published_at' => now()->subDays($sequence->index + 1),
            ])
            ->create();
    }

    public function test_archive_is_paginated_from_the_newest_post(): void
    {
        $this->get(route('posts.index'))
            ->assertOk()
            ->assertSee('25 posts')
            ->assertSeeInOrder(['Post 1', 'Post 2', 'Post 20'])
            ->assertDontSee('Post 21')
            ->assertSee('href="'.route('posts.index', ['page' => 2]).'"', false)
            ->assertSee('rel="next"', false);

        $this->get(route('posts.index', ['page' => 2]))
            ->assertOk()
            ->assertSeeInOrder(['Post 21', 'Post 25'])
            ->assertDontSee('Post 20<', false)
            ->assertSee('rel="prev"', false);
    }

    public function test_pages_past_the_last_one_return_not_found(): void
    {
        $this->get(route('posts.index', ['page' => 3]))->assertNotFound();
    }

    public function test_italian_archive_links_to_translated_slugs(): void
    {
        $this->get('/it/blog?page=2')
            ->assertOk()
            ->assertSee('href="'.url('/it/blog/articolo-25').'"', false)
            ->assertSee('Successiva');
    }

    public function test_posts_written_in_one_language_are_listed_only_in_that_language(): void
    {
        Post::factory()->onlyIn('it')->create(['headline' => ['it' => 'Solo in italiano'], 'published_at' => now()]);

        $this->get(route('posts.index'))->assertSee('25 posts')->assertDontSee('Solo in italiano');
        $this->get('/it/blog')->assertSee('26 articoli')->assertSee('Solo in italiano');
    }
}
