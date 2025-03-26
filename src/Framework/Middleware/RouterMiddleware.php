<?php
namespace Framework\Middleware;

use Framework\Router;
use Psr\Http\Message\ServerRequestInterface as Request;

class RouterMiddleware
{
    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function __invoke(Request $request, callable $next)
    {
        $route = $this->router->match($request);
        if ($route === null) {
            return $next($request);
        }
        $params = $route->getParams();
        $request = array_reduce(
            array: array_keys($params),
            callback: fn($request, $key) => $request->withAttribute($key, $params[$key]),
            initial: $request
        );
        $request = $request->withAttribute(get_class($route), $route);
        return $next($request);
    }
}
