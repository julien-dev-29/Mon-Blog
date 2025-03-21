<?php

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use DateTime;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostCrudAction extends CrudAction
{
    protected $viewPath = '@blog/admin/posts';
    protected $routePrefix = 'blog.admin';

    /**
     * @var CategoryTable
     */
    private $categoryTable;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $table,
        FlashService $flash,
        CategoryTable $categoryTable
    ) {
        parent::__construct($renderer, $router, $table, $flash);
        $this->categoryTable = $categoryTable;
    }
    protected function getParams(Request $request)
    {
        $params = array_filter(
            array: $request->getParsedBody(),
            callback: fn($key) =>
            in_array($key, ['name', 'slug', 'content', 'created_at', 'category_id']),
            mode: ARRAY_FILTER_USE_KEY
        );
        return $params = array_merge($params, [
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Retourne une instance de validator
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return Validator
     */
    protected function getValidator(Request $request): Validator
    {
        return parent::getValidator($request)
            ->required('name', 'slug', 'content', 'created_at', 'category_id')
            ->length('content', 10)
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->exists(
                key: 'category_id',
                table: $this->categoryTable->getTable(),
                pdo: $this->categoryTable->getPDO()
            )
            ->datetime('created_at')
            ->slug('slug');
    }

    /**
     * @return Post
     */
    protected function getNewEntity(): Post
    {
        $post = new Post();
        $post->created_at = new DateTime();
        return $post;
    }

    protected function formParams(array $params): array
    {
        $params['categories'] = $this->categoryTable->findList();
        return $params;
    }
}
