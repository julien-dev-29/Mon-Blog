<?php

namespace Tests\Framework;

use Framework\App;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

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
        $app = new App();
        $request = new ServerRequest('GET', '/blog');
        $response = $app->run($request);
        $this->assertStringContainsString('<h1>Bienvenu sur le blog</h1>',$response->getBody());
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
}