<?php

namespace App\Chat;

use Framework\Renderer\RendererInterface;

class ChatModule
{
    public const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(RendererInterface $renderer)
    {
        $renderer->addPath('chat', __DIR__ . '/views');
    }
}
