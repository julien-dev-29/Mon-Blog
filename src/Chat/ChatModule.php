<?php

namespace App\Chat;

use Framework\Renderer\RendererInterface;

class ChatModule
{
    public const DEFINITIONS = __DIR__ . '/config.php';
    public const MIGRATIONS = __DIR__ . '/db/migrations';
    public const SEEDS = __DIR__ . '/db/migrations';

    public function __construct(RendererInterface $renderer)
    {
        $renderer->addPath('chat', __DIR__ . '/views');
    }
}
