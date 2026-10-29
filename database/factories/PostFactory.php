<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $headline = Str::title(fake()->unique()->words(4, true));
        $slug = Str::slug($headline);

        return [
            'key' => $slug,
            'slug' => ['en' => $slug, 'it' => $slug],
            'headline' => ['en' => $headline, 'it' => $headline],
            'description' => null,
            'html' => ['en' => '<p>Body.</p>', 'it' => '<p>Testo.</p>'],
            'reading_time' => ['en' => 1, 'it' => 1],
            'package' => null,
            'published_at' => fake()->dateTimeBetween('-1 year'),
        ];
    }

    public function onlyIn(string $locale): static
    {
        return $this->state(fn (array $attributes) => collect(['slug', 'headline', 'html', 'reading_time'])
            ->mapWithKeys(fn (string $column) => [$column => [$locale => $attributes[$column][$locale]]])
            ->all());
    }
}
