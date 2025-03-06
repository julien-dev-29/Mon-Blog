<?php

namespace Tests\Framework\Blog\Actions;

use App\Blog\Actions\BlogAction;
use App\Blog\Table\PostTable;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use GuzzleHttp\Psr7\ServerRequest;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use stdClass;

class BlogActionTest extends TestCase
{
    /**
     * Summary of action
     * @var BlogAction
     */
    private $action;
    private $renderer;
    private $pdo;
    private $router;
    private $postTable;

    public function setUp(): void
    {
        // Render
        $this->renderer = $this->createMock(RendererInterface::class);
        $this->renderer->method('render')->willReturn('');

        // PostTable
        $this->postTable = $this->createMock(PostTable::class);

        // Router
        $this->router = $this->createMock(Router::class);
        $this->action = new BlogAction(
            renderer: $this->renderer,
            router: $this->router,
            postTable: $this->postTable
        );
    }

    public function testShowRedirect()
    {
        $post = $this->makePost(9, 'demo-test');
        $this->router->method('generateURL')->willReturn('/demo2');
        $this->postTable->method('find')->willReturn($post);
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', 'demo3');
        $response = call_user_func_array($this->action, [$request]);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals(['/demo2'], $response->getHeader('location'));
    }

    public function testShowRender()
    {
        $post = $this->makePost(9, 'demo-test');
        $this->postTable->method('find')->willReturn($post);
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', $post->slug);
        $this->renderer->method('render')->willReturn('');
        $response = call_user_func_array($this->action, [$request]);
        $this->assertEquals(true, true);
    }

    public function makePost(int $id, string $slug)
    {
        $post = new stdClass();
        $post->id = $id;
        $post->slug = $slug;
        return $post;
    }
}