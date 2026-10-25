<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetHttpCacheHeaders
{
    public function __construct(private SetCacheHeaders $cacheHeaders) {}

    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $options): Response
    {
        if (! config('app.http_cache')) {
            return $next($request);
        }

        return $this->cacheHeaders->handle($request, $next, $options);
    }
}
