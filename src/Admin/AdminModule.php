<?php

namespace App\Admin;

use Framework\Module;
use Framework\Renderer\RendererInterface;

class AdminModule extends Module
{
    public const DEFINITIONS = __DIR__ . '/config.php';
    public const SEEDS = __DIR__ . '/db/seeds';
    public function __construct(RendererInterface $renderer)
    {
        $renderer->addPath('admin', __DIR__ . '/views');
    }
}
