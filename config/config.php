<?php

use Framework\Renderer\{
    RendererInterface,
    TwigRendererFactory
};
use Framework\Router;
use Framework\Router\RouterTwigExtension;
use Psr\Container\ContainerInterface;

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
    RendererInterface::class => factory(TwigRendererFactory::class),
    PDO::class => function (ContainerInterface $container) {
        return new PDO(
            dsn: 'mysql:host=' . $container->get('database.host') .
            ';dbname=' . $container->get('database.name'),
            username: $container->get('database.username'),
            password: $container->get('database.password'),
            options: [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }
];