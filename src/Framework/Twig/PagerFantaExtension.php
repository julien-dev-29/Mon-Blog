<?php
namespace Framework\Twig;

use Framework\Router;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\TwitterBootstrap5View;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PagerFantaExtension extends AbstractExtension
{
    /**
     * @var Router
     */
    private $router;

    /**
     * Summary of __construct
     * @param \Framework\Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('paginate', [$this, 'paginate'], ['is_safe' => ['html']])
        ];
    }

    /**
     * Génére la pagination
     *
     * @param Pagerfanta $pagerfanta
     * @param string $route
     * @param array $routerParams
     * @param array $queryArgs
     * @return string
     */
    public function paginate(
        Pagerfanta $pagerfanta,
        string $route,
        array $routerParams = [],
        array $queryArgs = []
    ): string {
        $view = new TwitterBootstrap5View();
        return $view->render($pagerfanta, function ($page) use ($route, $routerParams, $queryArgs) {
            if ($page > 1) {
                $queryArgs['p'] = $page;
            }
            return $this->router->generateURL($route, $routerParams, $queryArgs);
        });
    }
}
