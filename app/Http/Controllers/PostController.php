<?php

namespace App\Http\Controllers;

use App\Support\SamplePosts;
use Illuminate\View\View;

class PostController extends Controller
{
    public function show(string $slug): View
    {
        $posts = SamplePosts::all()->values();
        $index = $posts->search(fn (array $post) => $post['slug'] === $slug);

        abort_if($index === false || ! view()->exists("posts.samples.{$slug}"), 404);

        $post = $posts[$index];

        return view("posts.samples.{$slug}", [
            'post' => $post,
            'package' => $post['package'] ? [...config("packages.{$post['package']}"), 'slug' => $post['package']] : null,
            'previous' => $posts->get($index + 1),
            'next' => $posts->get($index - 1),
        ]);
    }
}
