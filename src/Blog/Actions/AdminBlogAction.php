<?php

namespace App\Blog\Actions;

use App\Blog\Table\PostTable;
use Framework\Actions\RouterAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Framework\Validator;
use PDO;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminBlogAction
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
     * @var Router
     */
    private $router;

    /**
     * @var PostTable
     */
    private $postTable;

    /**
     * @var FlashService
     */
    private $flash;

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
    public function __construct(RendererInterface $renderer, Router $router, PostTable $postTable, FlashService $flash)
    {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->flash = $flash;
    }

    /**
     * Summary of __invoke
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return MessageInterface|string
     */
    public function __invoke(Request $request): MessageInterface|string
    {
        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if (substr((string) $request->getUri(), -3) === 'new') {
            return $this->create($request);
        }
        if ($request->getAttribute('id')) {
            return $this->edit(request: $request);
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
        $items = $this->postTable->findPaginated(perPage: 12, currentPage: $params['p'] ?? 1);
        return $this->renderer->render(
            view: '@blog/admin/index',
            params: compact('items')
        );
    }

    /**
     * Edite un article
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return string|\Psr\Http\Message\ResponseInterface
     */
    public function edit(Request $request): string|ResponseInterface
    {
        $errors = null;
        $item = $this->postTable->find($request->getAttribute('id'));
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $params['updated_at'] = date('Y-m-d H:i:s');
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->postTable->update($item->id, $params);
                $this->flash->success('L\'article a été modifié avec succés');
                return $this->redirect('blog.admin.index');
            }
            $errors = $validator->getErrors();
            $params['id'] = $item->id;
            $item = $params;
        }
        return $this->renderer->render(
            view: '@blog/admin/edit',
            params: compact('item', 'errors')
        );
    }

    public function create(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $params = array_merge($params, [
                'updated_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->postTable->insert($params);
                $this->flash->success('L\'article a été publié avec succés');
                return $this->redirect('blog.admin.index');
            }
            $errors = $validator->getErrors();
            $item = $params;
        }
        return $this->renderer->render(
            view: '@blog/admin/create',
            params: compact('item', 'errors')
        );
    }

    public function delete(Request $request)
    {
        $this->postTable->delete($request->getAttribute('id'));
        $this->flash->success('L\'article a été supprimé avec succés');
        return $this->redirect('blog.admin.index');
    }

    private function getParams(Request $request)
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug', 'content']);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Retourne une instance de validator
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return Validator
     */
    private function getValidator(Request $request): Validator
    {
        $validator = new Validator($request->getParsedBody());
        $validator
            ->required('name', 'slug', 'content')
            ->length('content', 10)
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->slug('slug');
        return $validator;
    }
}
