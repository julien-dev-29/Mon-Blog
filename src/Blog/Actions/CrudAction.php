<?php

namespace App\Blog\Actions;

use Framework\Actions\RouterAction;
use Framework\Database\Hydrator;
use Framework\Database\Table;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use PDO;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class CrudAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var FlashService
     */
    private $flash;

    /**
     * @var string
     */
    protected $viewPath;

    /**
     * @var string
     */
    protected $routePrefix;

    protected $messages = [
        'create' => 'L\'élément a été créé avec succés!',
        'edit' => 'L\'élément a été modifié avec succés!',
        'delete' => 'L\'élément a été supprimé avec succés!'
    ];

    /**
     * Trait
     */
    use RouterAction;

    /**
     * @param \Framework\Renderer\RendererInterface $renderer
     * @param \PDO $pdo
     * @param \Framework\Router $router
     */
    public function __construct(RendererInterface $renderer, Router $router, Table $table, FlashService $flash)
    {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->table = $table;
        $this->flash = $flash;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return MessageInterface|string
     */
    public function __invoke(Request $request): MessageInterface|string
    {
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);
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
     * Affiche un liste d'éléments
     *
     * @return string
     */
    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->table
            ->findAll()
            ->paginate(12, $params['p'] ?? 1);
        return $this->renderer->render(
            view: "$this->viewPath/index",
            params: compact('items')
        );
    }

    /**
     * Edite un élément
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return string|\Psr\Http\Message\ResponseInterface
     */
    public function edit(Request $request): string|ResponseInterface
    {
        $errors = null;
        $item = $this->table->find($request->getAttribute('id'));
        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->table->update(
                    id: $item->id,
                    params:
                    $this->getParams($request, $item)
                );
                $this->flash->success($this->messages['edit']);
                return $this->redirect("$this->routePrefix.index");
            }
            $errors = $validator->getErrors();
            Hydrator::hydrate($request->getParsedBody(), $item);
        }
        return $this->renderer->render(
            view: "$this->viewPath/edit",
            params: $this->formParams(compact('item', 'errors'))
        );
    }

    /**
     * Crée un nouvel élément
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return ResponseInterface|string
     */
    public function create(Request $request)
    {
        $item = $this->getNewEntity();
        $errors = null;
        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->table->insert($this->getParams($request, $item));
                $this->flash->success($this->messages['create']);
                return $this->redirect("$this->routePrefix.index");
            }
            $errors = $validator->getErrors();
            Hydrator::hydrate($request->getParsedBody(), $item);
        }
        return $this->renderer->render(
            view: "$this->viewPath/create",
            params: $this->formParams(compact('item', 'errors'))
        );
    }

    /**
     * Supprime un élément
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function delete(Request $request)
    {
        $this->table->delete($request->getAttribute('id'));
        $this->flash->success($this->messages['delete']);
        return $this->redirect("$this->routePrefix.index");
    }

    /**
     * Summary of getParamsRécupère les paramètres
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return array|object|null
     */
    protected function getParams(Request $request, $item)
    {
        return array_filter(
            array: $request->getParsedBody(),
            callback: fn($key): bool => in_array($key, []),
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
        return new Validator(array_merge(
            $request->getParsedBody(),
            $request->getUploadedFiles()
        ));
    }

    /**
     * @return mixed
     */
    protected function getNewEntity()
    {
        return [];
    }

    /**
     * Traite les paramètres à envoyer à la vue
     *
     * @param mixed $params
     * @return array
     */
    protected function formParams(array $params): array
    {
        return $params;
    }
}
