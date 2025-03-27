<?php
namespace Tests\Framework\Middleware;

use Framework\Middleware\DispatcherMiddleware;
use Framework\Router\Route;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

class DispatcherMiddlewareTest extends TestCase
{
    public function testDispatchWithStringResponse()
    {
        $container = $this->createMock(ContainerInterface::class);
        $route = $this->createMock(Route::class);
        $route->method('getCallback')->willReturn('some.service');
        $service = new class {
            public function __invoke()
            {
                return 'Yolo les kikis';
            }
        };
        $container->method('get')->willReturn($service);
        $request = new ServerRequest('GET', '/');
        $request = $request->withAttribute(Route::class, $route);
        $middleware = new DispatcherMiddleware($container);
        /**
         * @var Response
         */
        $response = $middleware($request, fn(): Response => new Response());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Yolo les kikis', (string) $response->getBody());
    }

    public function testDisptachWithResponseInterface()
    {
        $container = $this->createMock(ContainerInterface::class);

        $route = $this->createMock(Route::class);
        $route->method('getCallback')->willReturn('some.class');

        /**
         * @return ResponseInterface
         */
        $service = new class {
            public function __invoke()
            {
                $response = new Response(200, [], 'Yolo les kikis');
                return $response;
            }
        };
        $container->method('get')->willReturn($service);
        $request = new ServerRequest('GET', '/');
        $request = $request->withAttribute(Route::class, $route);
        $middleware = new DispatcherMiddleware($container);
        /**
         * @var Response
         */
        $response = $middleware($request, fn() => new Response());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Yolo les kikis', $response->getBody()->__tostring());
    }
}