<?php
namespace Tests\Framework\Middleware;

use Exception;
use Framework\Exception\CsrfInvalidException;
use Framework\Middleware\CsrfMiddleware;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;

class CsrfMiddlewareTest extends TestCase
{
    /**
     * @var CsrfMiddleware
     */
    private $middleware;

    /**
     * @var array
     */
    private $session;

    public function setUp(): void
    {
        $this->session = [];
        $this->middleware = new CsrfMiddleware($this->session);
    }

    public function testGetRequestPass()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->onlyMethods(['handle'])
            ->getMock();
        $handler->expects($this->once())
            ->method('handle');
        $request = new ServerRequest('GET', '/kiki');
        $this->middleware->process($request, $handler);
    }

    public function testPostWithoutCsrf()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->onlyMethods(['handle'])
            ->getMock();
        $handler->expects($this->never())
            ->method('handle');
        $request = new ServerRequest('POST', '/kiki');
        $this->expectException(CsrfInvalidException::class);
        $this->middleware->process($request, $handler);
    }

    public function testPostWithToken()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->onlyMethods(['handle'])
            ->getMock();
        $handler->expects($this->once())
            ->method('handle');
        $request = new ServerRequest('POST', '/kiki');
        $token = $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => $token]);
        $this->middleware->process($request, $handler);
    }

    public function testPostWithInvalidCsrf()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->onlyMethods(['handle'])
            ->getMock();
        $handler->expects($this->never())
            ->method('handle');
        $request = new ServerRequest('POST', '/kiki');
        $request = $request->withParsedBody(['_csrf' => 'kiki']);
        $this->expectException(CsrfInvalidException::class);
        $this->middleware->process($request, $handler);
    }

    public function testPostWithTokenOnce()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->onlyMethods(['handle'])
            ->getMock();
        $handler->expects($this->once())->method('handle');
        $request = new ServerRequest('POST', '/kiki');
        $token = $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => $token]);
        $this->middleware->process($request, $handler);
        $this->expectException(CsrfInvalidException::class);
        $this->middleware->process($request, $handler);
    }

    public function testTokenLimit()
    {
        for ($i=0; $i < 50; ++$i) { 
            $token = $this->middleware->generateToken();
        }
        $this->assertCount(50, $this->session['csrf']);
        $this->assertEquals($token, $this->session['csrf'][49]);
    }
}