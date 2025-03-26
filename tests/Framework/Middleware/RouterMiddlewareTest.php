<?php

namespace Tests\Framework\Middleware;

use Framework\Middleware\RouterMiddleware;
use Framework\Router;
use Framework\Router\Route;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class RouterMiddlewareTest extends TestCase
{
    public function testWithValidRoute()
    {
        $router = $this->createMock(Router::class);
        $route = $this->createMock(Route::class);
        $route->method('getParams')->willReturn(['id' => '123']);
        $router->method('match')->willReturn($route);
        $request = new ServerRequest('GET', '/');
        $middleware = new RouterMiddleware($router);
        $nextCalled = false;
        $middleware(
            $request,
            function (ServerRequestInterface $req) use (&$nextCalled) {
                $nextCalled = true;
                $this->assertEquals('123', $req->getAttribute('id'));
            }
        );
    }
}