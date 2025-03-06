<?php

namespace Framework;

use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Summary of App
 */
class App
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $modules = [];

    /**
     * Summary of __construct
     * @param \Psr\Container\ContainerInterface $container
     * @param array $modules
     */
    public function __construct(ContainerInterface $container, array $modules = [])
    {
        $this->container = $container;
        foreach ($modules as $module) {
            $this->modules = $container->get(id: $module);
        }
    }
    /**
     * Summary of run
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $uri = $request->getUri()->getPath();
        if (!empty($uri) && $uri[-1] === '/') {
            return (new Response())
                ->withStatus(301)
                ->withHeader('Location', substr(string: $uri, offset: 0, length: -1));
        }
        $router = $this->container->get(id: Router::class);
        $route = $router->match($request);
        if ($route === null) {
            return new Response(404, [], "<h1>Error 404</h1>");
        }
        $params = $route->getParams();
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);
        $callback = $route->getCallback();
        if (is_string($callback)) {
            $callback = $this->container->get($callback);
        }
        $response = call_user_func_array($callback, [$request]);
        if (is_string($response)) {
            return new Response(200, [], $response);
        } elseif ($response instanceof ResponseInterface) {
            return $response;
        } else {
            throw new \Exception('The response is not a string or an instance of ResponesInterface');
        }
    }

    /**
     * Summary of getContainer
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
