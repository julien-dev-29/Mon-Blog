<?php

namespace Tests\Framework\Modules;

use Framework\Router;
use stdClass;

class ErrorModule
{
    public function __construct(Router $router)
    {
        $router->get('/demo', function () {
            return new stdClass();
        }, 'demo');
    }
}