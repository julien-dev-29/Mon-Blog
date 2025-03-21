<?php

namespace App\Blog\Actions;

use App\Blog\Table\CategoryTable;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Classe qui implémente le crud sur les catégories
 */
class CategoryCrudAction extends CrudAction
{
    protected $viewPath = '@blog/admin/categories';
    protected $routePrefix = 'blog.categories.admin';

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        CategoryTable $table,
        FlashService $flash
    ) {
        parent::__construct($renderer, $router, $table, $flash);
    }
    protected function getParams(Request $request)
    {
        return array_filter(
            array: $request->getParsedBody(),
            callback: fn($key) =>
            in_array($key, ['name', 'slug']),
            mode: ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Retourne une instance de validator
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return Validator
     */
    protected function getValidator(Request $request): Validator
    {
        return parent::getValidator($request)
            ->required('name', 'slug')
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->unique(
                key: 'slug',
                table: $this->table->getTable(),
                pdo: $this->table->getPDO(),
                exclude: $request->getAttribute('id')
            )
            ->slug('slug');
    }
}
