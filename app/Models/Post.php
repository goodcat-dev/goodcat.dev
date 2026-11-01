<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Goodcat\L10n\Contracts\HasLocalizedRouteKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\HasTags;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Fillable(['key', 'slug', 'headline', 'description', 'html', 'reading_time', 'package', 'published_at'])]
#[Translatable(['slug', 'headline', 'description', 'html', 'reading_time'])]
class Post extends Model implements HasLocalizedRouteKey
{
    /** @use HasFactory<PostFactory> */
    use HasFactory, HasTags, HasTranslations;

    public function url(): string
    {
        return route('posts.show', $this);
    }

    public function getLocalizedRouteKey(string $locale, ?string $field = null): ?string
    {
        return $this->getTranslation('slug', $locale, false) ?: null;
    }

    public function previous(): ?self
    {
        return self::query()
            ->whereNotNull('slug->'.app()->getLocale())
            ->where('published_at', '<', $this->published_at)
            ->latest('published_at')
            ->first();
    }

    public function next(): ?self
    {
        return self::query()
            ->whereNotNull('slug->'.app()->getLocale())
            ->where('published_at', '>', $this->published_at)
            ->oldest('published_at')
            ->first();
    }

    /**
     * @return Attribute<string, never>
     */
    protected function title(): Attribute
    {
        return Attribute::get(fn () => str_replace('`', '', $this->headline));
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }
}
