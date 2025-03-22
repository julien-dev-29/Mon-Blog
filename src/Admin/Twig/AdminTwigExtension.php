<?php

namespace App\Admin\Twig;

use App\Admin\AdminWidgetInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdminTwigExtension extends AbstractExtension
{

    /**
     * @var array
     */
    private $widgets;

    public function __construct(array $widgets)
    {
        $this->widgets = $widgets;
    }
    public function getFunctions(): array
    {
        return [
            new TwigFunction('admin_menu', [$this, 'renderMenu'], ['is_safe' => ['html']])
        ];
    }

    public function renderMenu()
    {
        return array_reduce(
            array: $this->widgets,
            callback: fn(string $html, AdminWidgetInterface $widget)
            => $html . $widget->renderMenu(),
            initial: ''
        );
    }
}
