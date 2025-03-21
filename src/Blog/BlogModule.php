<?php

namespace App\Blog;

use App\Blog\Actions\BlogAction;
use App\Blog\Actions\CategoryCrudAction;
use App\Blog\Actions\CategoryShowAction;
use App\Blog\Actions\ChatBlogAction;
use App\Blog\Actions\PostCrudAction;
use App\Blog\Actions\PostIndexAction;
use App\Blog\Actions\PostShowAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Container\ContainerInterface;

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
    public function __construct(ContainerInterface $container)
    {
        $container->get(RendererInterface::class)->addPath('blog', __DIR__ . '/views');
        $container->get(RendererInterface::class)->addPath('chat', dirname(__DIR__) . '/Chat/views');
        $router = $container->get(Router::class);
        $router->get(
            path: $container->get('blog.prefix'),
            callable: PostIndexAction::class,
            name: 'blog.index'
        );
        $router->get(
            path: $container->get('blog.prefix') . '/[*:slug]-[i:id]',
            callable: PostShowAction::class,
            name: 'blog.show'
        );
        $router->get(
            path: $container->get('blog.prefix') . '/category/[*:slug]',
            callable: CategoryShowAction::class,
            name: 'blog.category'
        );
        if ($container->has('admin.prefix')) {
            $prefix = $container->get('admin.prefix');
            $router->crud("$prefix/posts", PostCrudAction::class, 'blog.admin');
            $router->crud("$prefix/categories", CategoryCrudAction::class, 'blog.categories.admin');
        }
        if ($container->has('chat.prefix')) {
            $prefix = $container->get('chat.prefix');
            $router->get($prefix, ChatBlogAction::class, 'chat.index');
        }
    }
}
