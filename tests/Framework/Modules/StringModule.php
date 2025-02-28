<?php

namespace Tests\Framework\Modules;

use Framework\Router;
use stdClass;

class StringModule
{
    public function __construct(Router $router)
    {
        $router->get('/demo', function () {
            return 'DEMO';
        }, 'demo');
    }
}