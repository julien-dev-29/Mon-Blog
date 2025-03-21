<?php

namespace App\Blog\Actions;

use App\Blog\Table\PostTable;
use Framework\Actions\RouterAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use PDO;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostShowAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var PostTable
     */
    private $postTable;

    /**
     * @var Router
     */
    private $router;

    /**
     * Trait
     */
    use RouterAction;

    /**
     * Summary of __construct
     * @param \Framework\Renderer\RendererInterface $renderer
     * @param \PDO $pdo
     * @param \Framework\Router $router
     */
    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $postTable
    ) {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->postTable = $postTable;
    }

    /**
     * Summary of __invoke
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return MessageInterface|string
     */
    public function __invoke(Request $request): MessageInterface|string
    {
        $slug = $request->getAttribute('slug');
        $id = $request->getAttribute('id');
        $post = $this->postTable->findWithCategory($id);
        if ($post->slug !== $slug) {
            return $this->redirect(path: 'blog.show', params: [
                'slug' => $post->slug,
                'id' => $post->id
            ]);
        }
        return $this->renderer->render(
            view: '@blog/show',
            params: compact(var_name: ['post'])
        );
    }
}
