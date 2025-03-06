<?php

namespace App\Blog;

use App\Blog\Actions\BlogAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;

/**
 * Summary of BlogModule
 */
class BlogModule extends Module
{
    public const DEFINITIONS = __DIR__ . '/config.php';
    public const MIGRATIONS = __DIR__ . '/db/migrations';
    public const SEEDS = __DIR__ . '/db/seeds';

    /**
     * Summary of __construct
     * @param string $prefix
     * @param \Framework\Router $router
     * @param \Framework\Renderer\RendererInterface $renderer
     */
    public function __construct(string $prefix, Router $router, RendererInterface $renderer)
    {
        $renderer->addPath('blog', __DIR__ . '/views');

        $router->get(
            path: $prefix,
            callable: BlogAction::class,
            name: 'blog.index'
        );

        $router->get(
            path: "$prefix/[*:slug]",
            callable: BlogAction::class,
            name: 'blog.show'
        );
    }
}
