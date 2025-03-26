<?php

namespace Tests\Framework\Middleware;

use Framework\Middleware\NotFoundMiddleware;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class NotFoundMiddlewareTest extends TestCase
{
    public function testReturn404Response()
    {
        $request = new ServerRequest('GET', "/");
        $middleware = new NotFoundMiddleware();
        $response = $middleware($request);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Erreur 404', (string) $response->getBody());
    }
}