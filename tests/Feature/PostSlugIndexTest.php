<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PostSlugIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_binding_looks_up_the_slug_through_the_index_of_each_locale(): void
    {
        $post = Post::factory()->create(['slug' => ['en' => 'indexed-post', 'it' => 'articolo-indicizzato']]);

        foreach (['en' => '/blog/indexed-post', 'it' => '/it/blog/articolo-indicizzato'] as $locale => $url) {
            DB::flushQueryLog();
            DB::enableQueryLog();

            $this->get($url)->assertOk()->assertViewHas('post', fn (Post $bound) => $bound->is($post));

            $lookup = collect(DB::getQueryLog())->firstWhere(fn (array $query) => str_contains($query['query'], 'json_extract("slug"'));
            $plan = DB::select("explain query plan {$lookup['query']}", $lookup['bindings']);

            $this->assertStringContainsString("USING INDEX posts_slug_{$locale}_unique", $plan[0]->detail);
        }
    }

    public function test_slugs_must_be_unique_within_a_locale(): void
    {
        Post::factory()->create(['slug' => ['en' => 'shared-slug', 'it' => 'slug-condiviso']]);

        $this->expectException(UniqueConstraintViolationException::class);

        Post::factory()->create(['slug' => ['en' => 'other-slug', 'it' => 'slug-condiviso']]);
    }

    public function test_slug_can_be_reused_across_locales_and_left_untranslated(): void
    {
        Post::factory()->create(['slug' => ['en' => 'same-slug', 'it' => 'same-slug']]);
        Post::factory()->onlyIn('en')->create();
        Post::factory()->onlyIn('en')->create();

        $this->assertSame(3, Post::query()->count());
    }
}
