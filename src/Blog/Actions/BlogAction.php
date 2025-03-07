<?php

namespace App\Blog\Actions;

use App\Blog\Table\PostTable;
use Framework\Actions\RouterAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use GuzzleHttp\Psr7\Response;
use Kint\Kint;
use PDO;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogAction
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
     * Summary of router
     * @var Router
     */
    private $router;

    private $postTable;

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
    public function __construct(RendererInterface $renderer, Router $router, PostTable $postTable)
    {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->postTable = $postTable;
    }

    /**
     * Summary of __invoke
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return MessageInterface|string
     */
    public function __invoke(Request $request): MessageInterface|string
    {
        if ($request->getAttribute('id')) {
            return $this->show(request: $request);
        }
        return $this->index($request);
    }

    /**
     * Summary of index
     * @return string
     */
    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $posts = $this->postTable->findPaginated(perPage: 12, currentPage: $params['p'] ?? 1);
        return $this->renderer->render(
            view: '@blog/index',
            params: compact(var_name: 'posts')
        );
    }

    /**
     * Summary of show
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return string|\Psr\Http\Message\MessageInterface
     */
    public function show(Request $request): MessageInterface|string
    {
        $slug = $request->getAttribute('slug');
        $post = $this->postTable->find($request->getAttribute('id'));
        if ($post->slug !== $slug) {
            return $this->redirect(path: 'blog.show', params: [
                'slug' => $post->slug,
                'id' => $post->id
            ]);
        }
        return $this->renderer->render(
            view: '@blog/show',
            params: compact(var_name: 'post')
        );
    }
}
