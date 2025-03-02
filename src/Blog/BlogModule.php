<?php

namespace App\Blog;

use App\Framework\Renderer;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Summary of BlogModule
 */
class BlogModule
{
    /**
     * Summary of renderer
     * @var Renderer
     */
    private $renderer;

    /**
     * Summary of __construct
     * @param \Framework\Router $router
     */
    public function __construct(Router $router, Renderer $renderer)
    {
        $this->renderer = $renderer;
        $this->renderer->addPath('blog', __DIR__ . '/views');
        $router->get('/blog', [$this, 'index'], 'blog.index');
        $router->get(
            path: '/blog/[*:slug]',
            callable: [$this, 'show'],
            name: 'blog.show'
        );
    }

    public function index(Request $request): string
    {
        return $this->renderer->render('@blog/index');
    }

    public function show(Request $request): string
    {
        return $this->renderer->render('@blog/show', [
            'slug' => $request->getAttribute('slug')
        ]);
    }
}
