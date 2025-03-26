<?php

namespace Framework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TrailingSlashMiddleware
{
    public function __invoke(Request $request, callable $next)
    {
        $uri = $request->getUri()->getPath();
        if (!empty($uri) && $uri[-1] === '/') {
            return (new Response())
                ->withStatus(301)
                ->withHeader(
                    header: 'Location',
                    value: substr(string: $uri, offset: 0, length: -1)
                );
        }
        return $next($request);
    }
}
