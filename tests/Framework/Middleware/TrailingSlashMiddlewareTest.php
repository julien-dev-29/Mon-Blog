<?php

namespace Tests\Framework\Middleware;

use Framework\Middleware\TrailingSlashMiddleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class TrailingSlashMiddlewareTest extends TestCase
{
    public function testWithSlash()
    {
        $request = new ServerRequest('GET', '/kiki/');
        $middleware = new TrailingSlashMiddleware();
        $nextCalled = false;
        /**
         * @var Response
         */
        $response = $middleware(
            $request,
            fn() => new Response()
        );
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals('/kiki', $response->getHeaderLine('Location'));
    }
}