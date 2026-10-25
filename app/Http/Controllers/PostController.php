<?php

namespace App\Http\Controllers;

use App\Content\Docs;
use App\Content\Post;
use App\Content\Posts;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\View\View;

class PostController extends Controller
{
    private const int PER_PAGE = 20;

    public function index(Posts $posts): View
    {
        $all = $posts->all(app()->getLocale());
        $page = Paginator::resolveCurrentPage();

        abort_if($page > 1 && $page > (int) ceil($all->count() / self::PER_PAGE), 404);

        $paginator = new LengthAwarePaginator(
            $all->forPage($page, self::PER_PAGE)->values(),
            $all->count(),
            self::PER_PAGE,
            $page,
            ['path' => Paginator::resolveCurrentPath()],
        );

        return view('posts.index', [
            'paginator' => $paginator,
            'years' => $paginator->getCollection()->groupBy(fn (Post $post) => $post->publishedAt->year),
        ]);
    }

    public function show(Docs $docs, Posts $posts, Post $post): View
    {
        $locale = app()->getLocale();
        $all = $posts->all($locale);
        $index = $all->search(fn (Post $candidate) => $candidate->key === $post->key);

        return view('posts.show', [
            'post' => $post,
            'html' => $posts->html($post, $locale),
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
