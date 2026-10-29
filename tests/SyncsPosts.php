<?php

namespace Tests;

use App\Content\Posts;
use Illuminate\Foundation\Testing\RefreshDatabase;

trait SyncsPosts
{
    use RefreshDatabase;

    protected function setUpSyncsPosts(): void
    {
        $this->app->make(Posts::class)->sync();
    }
}
