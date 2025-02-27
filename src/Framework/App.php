<?php

namespace Framework;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Summary of App
 */
class App
{
    /**
     * Summary of run
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $uri = $request->getUri()->getPath();
        if (!empty($uri) && $uri[-1] === '/') {
            return (new Response())
                ->withStatus(301)
                ->withHeader(
                    'Location',
                    substr(
                        string: $uri,
                        offset: 0,
                        length:
                        -1
                    )
                );
        }

        if ($uri === '/blog') {
            return new Response(200, [], '<h1>Bienvenu sur le blog</h1>');
        }

        $response = new Response(404, [], '<h1>Error 404</h1>');
        return $response;
    }
}
