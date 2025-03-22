<?php

namespace Tests\Framework\Modules;

use Framework\Router;
use stdClass;

class ErrorModule
{
    public function __construct(Router $router)
    {
        $router->get(
            path: '/demo',
            callable: fn()
            => new stdClass()
            ,
            name: 'demo'
        );
    }
}