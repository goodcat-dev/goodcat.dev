<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const array LOCALES = ['en', 'it'];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (self::LOCALES as $locale) {
            DB::statement("create unique index posts_slug_{$locale}_unique on posts (json_extract(slug, '$.\"{$locale}\"'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (self::LOCALES as $locale) {
            DB::statement("drop index posts_slug_{$locale}_unique");
        }
    }
};
