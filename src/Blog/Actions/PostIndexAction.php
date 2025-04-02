<?php

namespace App\Blog\Actions;

use App\Blog\Table\PostTable;
use App\Blog\Table\CategoryTable;
use Framework\Actions\RouterAction;
use Framework\Renderer\RendererInterface;
use PDO;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Summary of PostIndexAction
 */
class PostIndexAction
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
     * @var CategoryTable
     */
    private $categoryTable;

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
        PostTable $postTable,
        CategoryTable $categoryTable
    ) {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->categoryTable = $categoryTable;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return MessageInterface|string
     */
    public function __invoke(Request $request): MessageInterface|string
    {
        var_dump("yolo");
        $params = $request->getQueryParams();
        $posts = $this->postTable
            ->findPublic()
            ->paginate(12, $params['p'] ?? 1);
        $categories = $this->categoryTable->findAll();
        $page = $params['p'] ?? 1;
        return $this->renderer->render(
            view: '@blog/index',
            params: compact(['posts', 'categories', 'page'])
        );
    }
}
