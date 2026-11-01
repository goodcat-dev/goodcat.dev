<?php

namespace App\Http\Controllers;

use App\Content\Docs;
use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PostController extends Controller
{
    private const int PER_PAGE = 20;

    private const int FEED_SIZE = 20;

    public function index(): View
    {
        $paginator = Post::query()
            ->whereNotNull('slug->'.app()->getLocale())
            ->latest('published_at')
            ->paginate(self::PER_PAGE);

        abort_if($paginator->currentPage() > 1 && $paginator->isEmpty(), 404);

        return view('posts.index', [
            'paginator' => $paginator,
            'years' => $paginator->getCollection()->groupBy(fn (Post $post) => $post->published_at->year),
        ]);
    }

    public function feed(): Response
    {
        $posts = Post::query()
            ->with('tags')
            ->whereNotNull('slug->'.app()->getLocale())
            ->latest('published_at')
            ->take(self::FEED_SIZE)
            ->get();

        return response()
            ->view('posts.feed', ['posts' => $posts])
            ->header('Content-Type', 'application/atom+xml; charset=UTF-8');
    }

    public function show(Docs $docs, Post $post): View
    {
        return view('posts.show', [
            'post' => $post,
            'html' => $post->getTranslation('html', app()->getLocale(), false),
            'package' => $post->package ? [
                ...config("packages.{$post->package}"),
                'slug' => $post->package,
                'url' => $docs->url($post->package) ?? config("packages.{$post->package}.repository"),
            ] : null,
            'previous' => $post->previous(),
            'next' => $post->next(),
        ]);
    }
}
