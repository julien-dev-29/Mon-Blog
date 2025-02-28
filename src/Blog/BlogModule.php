<?php

namespace App\Blog;

use Framework\Router;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogModule
{
    public function __construct(Router $router)
    {
        $router->get('/blog', [$this, 'index'], 'blog.index');
        $router->get(
            path: '/blog/[*:slug]',
            callable: [$this, 'show'],
            name: 'blog.show'
        );
    }

    public function index(Request $request)
    {
        return '<h1>Bienvenue sur le blog</h1>';
    }

    public function show(Request $request)
    {
        return '<h1>Bienvenue sur l\'article ' . $request->getAttribute('slug') . '</h1>';
    }
}
