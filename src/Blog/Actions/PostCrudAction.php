<?php

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
use App\Blog\PostUpload;
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
    private $postUpload;

    /**
     * @var CategoryTable
     */
    private $categoryTable;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $table,
        FlashService $flash,
        CategoryTable $categoryTable,
        PostUpload $postUpload
    ) {
        parent::__construct($renderer, $router, $table, $flash);
        $this->categoryTable = $categoryTable;
        $this->postUpload = $postUpload;
    }

    public function delete(Request $request)
    {
        $post = $this->table->find($request->getAttribute('id'));
        $this->postUpload->delete($post->image);
        return parent::delete($request);
    }

    /**
     * Summary of getParams
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param mixed $post
     * @return array|object|null
     */
    protected function getParams(Request $request, $post)
    {
        $params = array_merge(
            $request->getParsedBody(),
            $request->getUploadedFiles()
        );
        $image = $this->postUpload->upload($params['image'], $post->image);
        if ($image) {
            $params['image'] = $image;
        } else {
            unset($params['image']);
        }
        $params = array_filter(
            array: $params,
            callback: fn($key) =>
            in_array($key, ['name', 'slug', 'content', 'created_at', 'category_id', 'image', 'published']),
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
        /**
         * @var Validator
         */
        $validator = parent::getValidator($request)
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
            ->extension('image', ['jpg', 'png'])
            ->slug('slug');
        if ($request->getAttribute('id') === null) {
            $validator->uploaded('image');
        }
        return $validator;
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
