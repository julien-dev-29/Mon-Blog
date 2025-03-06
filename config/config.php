<?php

use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use Framework\Router;
use Framework\Router\RouterTwigExtension;

use function DI\{create, factory, get};

return [
    'database.host' => 'localhost',
    'database.username' => 'root',
    'database.password' => 'JuR0ll1982!',
    'database.name' => 'monframework',
    'views.path' => dirname(__DIR__) . '/templates',
    'twig.extensions' => [
        get(RouterTwigExtension::class)
    ],
    Router::class => create(),
    RendererInterface::class => factory(TwigRendererFactory::class)
];