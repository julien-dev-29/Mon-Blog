<?php

use App\Admin\Twig\AdminTwigExtension;
use App\Admin\AdminModule;
use App\Admin\DashboardAction;

use function DI\get;
use function DI\autowire;
use function DI\add;
use function DI\create;

return [
    'admin.prefix' => '/admin',
    'admin.widgets' => [],
    AdminTwigExtension::class => create()
        ->constructor(get('admin.widgets')),
    AdminModule::class => autowire()
        ->constructorParameter('prefix', get('admin.prefix')),
    DashboardAction::class => autowire()
        ->constructorParameter('widgets', get('admin.widgets'))
];
