<?php
namespace Framework;

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Summary of App
 */
class App implements RequestHandlerInterface
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
     * @var string
     */
    private $definition;

    /**
     * @var string[]
     */
    private $middlewares;

    /**
     * @var int
     */
    private $index = 0;

    /**
     * Summary of __construct
     * @param \Psr\Container\ContainerInterface $container
     * @param array $modules
     */
    public function __construct(string $definition)
    {
        $this->definition = $definition;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function run(Request $request): ResponseInterface
    {
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }
        return $this->handle($request);
    }

    /**
     * Chaîne les middlewares
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @throws \Exception
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(Request $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        if ($middleware === null) {
            throw new Exception('Le middleware n\'éxiste pas');
        } elseif (is_callable($middleware)) {
            return call_user_func_array(
                callback: $middleware,
                args: [$request, [$this, 'handle']]
            );
        } elseif ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
        return $middleware->process($request, $this);
    }

    /**
     * Ajoute un middleware
     *
     * @param string $middleware
     * @return App
     */
    public function pipe(string $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * Ajoute un module
     *
     * @param string $module
     * @return App
     */
    public function addModule(string $module): self
    {
        $this->modules[] = $module;
        return $this;
    }

    /**
     * Retourne une instance du Container
     *
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $builder = new ContainerBuilder();
            $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
            $dotenv->load();
            $env = $_ENV['ENV'] ?? 'production';
            if ($env === 'production') {
                $builder->enableCompilation('tmp/proxies');
            }
            $builder->addDefinitions($this->definition);
            foreach ($this->modules as $module) {
                if ($module::DEFINITIONS !== null) {
                    $builder->addDefinitions($module::DEFINITIONS);
                }
            }
            $this->container = $builder->build();
        }
        return $this->container;
    }

    private function getMiddleware()
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            $middleware = $this->container->get($this->middlewares[$this->index]);
            $this->index++;
            return $middleware;
        }
        return null;
    }
}
