<?php

namespace Framework;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Summary of App
 */
class App
{
    /**
     * Summary of router
     * @var Router
     */
    private $router;

    /**
     * Summary of modules
     * @var array
     */
    private $modules = [];

    /**
     * Summary of __construct
     * @param string[] $modules Liste des modules à charge
     */
    public function __construct(array $modules = [], array $dependencies = [])
    {
        $this->router = new Router();
        if (array_key_exists('renderer', $dependencies)) {
            $dependencies['renderer']->addGlobal('router', $this->router);
        }
        foreach ($modules as $module) {
            $this->modules = new $module($this->router, $dependencies['renderer']);
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

        $route = $this->router->match($request);
        if ($route === null) {
            return new Response(404, [], "<h1>Error 404</h1>");
        }
        $params = $route->getParams();
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);
        $response = call_user_func_array($route->getCallback(), [$request]);
        if (is_string($response)) {
            return new Response(200, [], $response);
        } elseif ($response instanceof ResponseInterface) {
            return $response;
        } else {
            throw new \Exception('The response is not a string or an instance of ResponesInterface');
        }
    }
}
