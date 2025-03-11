<?php

namespace App\Blog\Actions;

use Framework\Actions\RouterAction;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Framework\Router;

class ChatBlogAction
{
    private $router;
    private $renderer;

    /**
     * Trait
     */
    use RouterAction;

    public function __construct(RendererInterface $renderer, Router $router)
    {
        $this->router = $router;
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request): MessageInterface|string
    {
        if ($request->getAttribute('id')) {
            //return $this->show(request: $request);
        }
        return $this->index($request);
    }

    public function index(Request $request)
    {
        return $this->renderer->render('@chat/index');
    }
}
