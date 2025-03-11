<?php

namespace Framework;

use AltoRouter;
use App\Blog\Actions\AdminBlogAction;
use Psr\Http\Message\ServerRequestInterface;
use Framework\Router\Route;

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
     * @param string|callable $callable
     * @param string $name
     * @return void
     */
    public function get(string $path, $callable, ?string $name = null)
    {
        $this->router->map("GET", $path, $callable, $name);
    }

    public function post(string $path, $callable, ?string $name = null)
    {
        $this->router->map("POST", $path, $callable, $name);
    }

    public function put(string $path, $callable, ?string $name = null)
    {
        $this->router->map("PUT", $path, $callable, $name);
    }

    public function delete(string $path, $callable, ?string $name = null)
    {
        $this->router->map("DELETE", $path, $callable, $name);
    }

    /**
     * Génère les routes du crud des articles
     * @param string $path
     * @param string|callable $callable
     * @param string|null $prefixName
     * @return void
     */
    public function crudBlog(string $path, $callable, ?string $prefixName)
    {
        $this->get($path, $callable, "$prefixName.index");
        $this->get("$path/[i:id]", $callable, "$prefixName.edit");
        $this->post("$path/[i:id]", $callable, "$prefixName.update");
        $this->get("$path/new", $callable, "$prefixName.create");
        $this->post("$path/new", $callable, "$prefixName.created");
        $this->delete("$path/delete/[i:id]", $callable, "$prefixName.delete");
    }

    /**
     * Génère une URL avec des paramètres
     *
     * @param string $routeName
     * @param array $params
     * @return string
     */
    public function generateURL(string $routeName, array $params = [], array $queryParams = [])
    {
        $uri = $this->router->generate($routeName, $params);
        if (!empty($queryParams)) {
            return $uri . '?' . http_build_query($queryParams);
        }
        return $uri;
    }

    /**
     * Summary of match
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return void
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        $result = $this->router->match($request->getUri()->getPath(), $request->getMethod());

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
