<?php

namespace App\Blog;

use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Summary of BlogModule
 */
class BlogModule
{

    private $renderer;

    /**
     * Summary of __construct
     * @param \Framework\Router $router
     * @param \Framework\Renderer\RendererInterface $renderer
     */
    public function __construct(Router $router, RendererInterface $renderer)
    {
        $this->renderer = $renderer;
        $this->renderer->addPath('blog', __DIR__ . '/views');
        $router->get(
            path: '/blog',
            callable: [$this, 'index'],
            name: 'blog.index'
        );
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
