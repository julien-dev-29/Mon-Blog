<?php

namespace Framework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class NotFoundMiddleware
{
    public function __invoke(Request $request)
    {
        return new Response(404, [], 'Erreur 404');
    }
}
