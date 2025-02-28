<?php

namespace Tests\Framework;

use App\Blog\BlogModule;
use Exception;
use Framework\App;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Tests\Framework\Modules\ErrorModule;
use Tests\Framework\Modules\StringModule;

/**
 * Summary of AppTest
 */
class AppTest extends TestCase
{
    /**
     * Summary of testRedirectTrailingSlash
     * @return void
     */
    public function testRedirectTrailingSlash()
    {
        $app = new App();
        $request = new ServerRequest(
            method: 'GET',
            uri: '/demoslash/'
        );
        $response = $app->run(request: $request);
        $this->assertContains('/demoslash', $response->getHeader('Location'));
        $this->assertEquals(301, $response->getStatusCode());
    }

    public function testBlog()
    {
        $app = new App([
            BlogModule::class
        ]);
        $request = new ServerRequest('GET', '/blog');
        $response = $app->run($request);
        $this->assertStringContainsString('<h1>Bienvenue sur le blog</h1>', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());

        $requestSingle = new ServerRequest('GET', '/blog/article-de-test');
        $responseSingle = $app->run($requestSingle);
        $this->assertStringContainsString('<h1>Bienvenue sur l\'article article-de-test</h1>', $responseSingle->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testError404()
    {
        $app = new App();
        $request = new ServerRequest('GET', '/aze');
        $response = $app->run($request);
        $this->assertStringContainsString('<h1>Error 404</h1>', $response->getBody());
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testConvertStringToResponse()
    {
        $app = new App(
            [
                StringModule::class
            ]
        );
        $request = new ServerRequest('GET', '/demo');
        $response = $app->run($request);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('DEMO', (string) $response->getBody());
    }

    public function testThrowExceptionIfNoResponseSent()
    {
        $app = new App(
            [
                ErrorModule::class
            ]
        );
        $request = new ServerRequest('GET', '/demo');
        $this->expectException(Exception::class);
        $app->run($request);
    }
}