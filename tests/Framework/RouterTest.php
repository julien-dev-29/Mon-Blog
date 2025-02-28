<?php

namespace Tests\Framework;

use Framework\Router;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    private $router;
    public function setUp(): void
    {
        $this->router = new Router();
    }

    public function testGetMethod()
    {
        $request = new ServerRequest('GET', '/blog');
        $this->router->get('/blog', function () {
            return 'Hello';
        }, 'blog');
        $route = $this->router->match($request);
        $this->assertEquals('blog', $route->getName());
        $this->assertEquals('Hello', call_user_func_array($route->getCallback(), [$request]));
    }

    public function testGetMethodIfURLDoesNotExists()
    {
        $request = new ServerRequest('GET', '/blog');
        $this->router->get('/blogaze', function () {
            return 'Hello';
        }, 'blog');
        $route = $this->router->match($request);
        $this->assertNull($route);
    }

    public function testGetMethodWithParameters()
    {
        $request = new ServerRequest('GET', '/blog/mon-slug-8');
        $this->router->get('/blog/[*:slug]-[i:id]', function () {
            return 'Hello';
        }, 'posts.show');
        $route = $this->router->match($request);
        $this->assertNotNull($route);
        $this->assertEquals('posts.show', $route->getName());
        $this->assertEquals('Hello', call_user_func_array($route->getCallback(), [$request]));
        $this->assertEquals(['slug' => 'mon-slug', 'id' => '8'], $route->getParams());
    }

    public function testGenerateURI()
    {
        $this->router->get(
            '/blog/[*:slug]-[i:id]',
            function () {
                return "Jurol yolo";
            }
            ,
            "blog"
        );
        $uri = $this->router->generateURL('blog', [
            'slug' => 'mon-article',
            'id' => 18
        ]);
        $this->assertEquals('/blog/mon-article-18', $uri);
    }
}