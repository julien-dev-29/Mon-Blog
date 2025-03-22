<?php

namespace App\Admin;

use App\Admin\Twig\AdminTwigExtension;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRenderer;
use Framework\Router;

class AdminModule extends Module
{
    public const DEFINITIONS = __DIR__ . '/config.php';
    public const SEEDS = __DIR__ . '/db/seeds';
    public function __construct(
        RendererInterface $renderer,
        Router $router,
        AdminTwigExtension $adminTwigExtension,
        string $prefix
    ) {
        $renderer->addPath('admin', __DIR__ . '/views');
        $router->get($prefix, DashboardAction::class, 'admin');
        if ($renderer instanceof TwigRenderer) {
            $renderer->getTwig()->addExtension($adminTwigExtension);
        }
    }
}
