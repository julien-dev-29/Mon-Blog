<?php

namespace Framework;

use AltoRouter;
use Psr\Http\Message\ServerRequestInterface;
use Framework\Router\Route;
use function PHPUnit\Framework\isEmpty;

/**
 * Summary of Router
 */
class Router
{
    /**
     * Summary of router
     * @var AltoRouter
     */
    private $router;
    public function __construct()
    {
        $this->router = new AltoRouter();
    }
    /**
     * Summary of get
     *
     * @param string $path
     * @param callable $callable
     * @param string $name
     * @return void
     */
    public function get(string $path, callable $callable, string $name)
    {
        $this->router->map("GET", $path, $callable, $name);
    }

    public function generateURL(string $routeName, array $params)
    {
        return $this->router->generate($routeName, $params);
    }

    /**
     * Summary of match
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return void
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        $result = $this->router->match($request->getUri()->getPath());
        if ($result !== null && isset($result['name'])) {
            $route = new Route(
                name: $result['name'],
                callable: $result['target'],
                params: $result['params']
            );
            return $route;
        }
        return null;
    }
}
