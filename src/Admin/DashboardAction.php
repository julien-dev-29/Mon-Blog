<?php

namespace App\Admin;

use Framework\Renderer\RendererInterface;

class DashboardAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var AdminWidgetInterface[]
     */
    private $widgets;

    public function __construct(RendererInterface $renderer, array $widgets)
    {
        $this->renderer = $renderer;
        $this->widgets = $widgets;
    }

    public function __invoke()
    {
        $widgets = array_reduce(
            array: $this->widgets,
            callback: fn($html, AdminWidgetInterface $widget)
            => $html . $widget->render(),
            initial: ''
        );
        return $this->renderer->render('@admin/dashboard', compact('widgets'));
    }
}
