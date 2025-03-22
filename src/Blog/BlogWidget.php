<?php

namespace App\Blog;

use App\Blog\Table\PostTable;

use App\Admin\AdminWidgetInterface;
use Framework\Renderer\RendererInterface;

class BlogWidget implements AdminWidgetInterface
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var PostTable
     */
    private $postTable;

    /**
     * @param RendererInterface $renderer
     */
    public function __construct(RendererInterface $renderer, PostTable $postTable)
    {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $count = $this->postTable->count();
        return $this->renderer->render(
            view: '@blog/admin/widget',
            params: compact('count')
        );
    }

    public function renderMenu(): string
    {
        return $this->renderer->render('@blog/admin/menu');
    }
}
