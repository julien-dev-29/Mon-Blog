<?php

use App\Blog\BlogWidget;

use function DI\add;
use function DI\get;

return [
    'blog.prefix' => '/blog',
    'chat.prefix' => '/chat',
    'admin.widgets' => add([
        get(BlogWidget::class)
    ])
];
