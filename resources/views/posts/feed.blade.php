{!! '<'.'?xml version="1.0" encoding="utf-8"?>' !!}
<feed xmlns="http://www.w3.org/2005/Atom" xml:lang="{{ app()->getLocale() }}">
    <id>{{ route('posts.feed') }}</id>
    <title>goodcat.dev</title>
    <subtitle>Design, Code, Repeat.</subtitle>
    <link rel="self" type="application/atom+xml" href="{{ route('posts.feed') }}" />
    <link rel="alternate" type="text/html" href="{{ route('posts.index') }}" />
    <updated>{{ ($posts->first()?->published_at ?? now())->toAtomString() }}</updated>
    <author>
        <name>Matteo Palazzo</name>
        <uri>{{ route('home') }}</uri>
    </author>
@foreach ($posts as $post)
    <entry>
        <id>{{ $post->url() }}</id>
        <title>{{ $post->title }}</title>
        <link rel="alternate" type="text/html" href="{{ $post->url() }}" />
        <published>{{ $post->published_at->toAtomString() }}</published>
        <updated>{{ $post->published_at->toAtomString() }}</updated>
@if ($description = $post->getTranslation('description', app()->getLocale(), false))
        <summary>{{ $description }}</summary>
@endif
@foreach ($post->tags as $tag)
        <category term="{{ $tag->name }}" />
@endforeach
    </entry>
@endforeach
</feed>
