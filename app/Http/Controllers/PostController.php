<?php

namespace App\Http\Controllers;

use App\Content\Docs;
use App\Content\Post;
use App\Content\Posts;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Posts $posts): View
    {
        return view('posts.index', [
            'years' => $posts->all(app()->getLocale())->groupBy(fn (Post $post) => $post->publishedAt->year),
        ]);
    }

    public function show(Docs $docs, Posts $posts, Post $post): View
    {
        $all = $posts->all(app()->getLocale());
        $index = $all->search(fn (Post $candidate) => $candidate->key === $post->key);

        return view('posts.show', [
            'post' => $post,
            'package' => $post->package ? [
                ...config("packages.{$post->package}"),
                'slug' => $post->package,
                'url' => $docs->url($post->package) ?? config("packages.{$post->package}.repository"),
            ] : null,
            'previous' => $all->get($index + 1),
            'next' => $all->get($index - 1),
        ]);
    }
}
