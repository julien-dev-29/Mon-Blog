<?php

namespace App\Blog\Actions;

use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use Framework\Actions\RouterAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use PDO;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class CategoryShowAction
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
     * @var CategoryTable
     */
    private $categoryTable;

    /**
     * @var PostTable
     */
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
    public function __construct(
        RendererInterface $renderer,
        CategoryTable $categoryTable,
        PostTable $postTable
    ) {
        $this->renderer = $renderer;
        $this->categoryTable = $categoryTable;
        $this->postTable = $postTable;
    }

    /**
     * Summary of __invoke
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return MessageInterface|string
     */
    public function __invoke(Request $request): MessageInterface|string
    {
        $params = $request->getQueryParams();
        $slug = $request->getAttribute('slug');
        $category = $this->categoryTable->findBy('slug', $slug);
        $posts = $this->postTable->findPaginatedPublicByCategory(12, $params['p'] ?? 1, $category->id);
        $categories = $this->categoryTable->findAll();
        $page = $params['p'] ?? 1;
        return $this->renderer->render(
            view: '@blog/index',
            params: compact(var_name: ['posts', 'categories', 'category', 'page'])
        );
    }
}
