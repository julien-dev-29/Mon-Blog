<?php

namespace Tests\Framework\Blog\Actions;

use App\Blog\Actions\PostIndexAction;
use App\Blog\Actions\PostShowAction;
use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class PostShowActionTest extends TestCase
{
    /**
     * Summary of action
     * @var PostIndexAction
     */
    private $action;
    private $renderer;
    private $pdo;
    private $router;
    private $postTable;

    public function setUp(): void
    {
        $this->renderer = $this->createMock(RendererInterface::class);
        $this->renderer->method('render')->willReturn('');
        $this->postTable = $this->createMock(PostTable::class);
        $this->router = $this->createMock(Router::class);
        $this->action = new PostShowAction(
            renderer: $this->renderer,
            router: $this->router,
            postTable: $this->postTable
        );
    }

    public function testShowRedirect()
    {
        $post = $this->makePost(9, 'demo-test');
        $this->router->method('generateURL')->willReturn('/demo2');
        $this->postTable->method('findWithCategory')->willReturn($post);
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
        $this->postTable->method('findWithCategory')->willReturn($post);
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', $post->slug);
        $this->renderer->method('render')->willReturn('');
        $response = call_user_func_array($this->action, [$request]);
        $this->assertEquals(true, true);
    }

    /**
     * Retourne une nouvelle instance de Post
     * 
     * @param int $id
     * @param string $slug
     * @return Post
     */
    public function makePost(int $id, string $slug): Post
    {
        $post = new Post();
        $post->id = $id;
        $post->slug = $slug;
        return $post;
    }
}