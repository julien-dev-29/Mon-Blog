<?php

namespace Framework\Router;

use Framework\Router;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RouterTwigExtension extends AbstractExtension
{
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('path', [$this, 'pathFor']),
            new TwigFunction('is_subpath', [$this, 'isSubPath'])
        ];
    }

    public function pathFor(string $path, array $params = []): string
    {
        return $this->router->generateURL($path, $params);
    }

    /**
     * @return bool|int
     */
    public function isSubPath(string $path): bool|int
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $expectedUri = $this->router->generateURL($path);
        return strpos($uri, $expectedUri) !== false;
    }
}
