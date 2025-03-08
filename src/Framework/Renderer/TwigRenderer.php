<?php

namespace Framework\Renderer;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRenderer implements RendererInterface
{
    /**
     * Summary of twig
     * @var Environment
     */
    private $twig;

    /**
     * Summary of __construct
     * @param \Twig\Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Summary of addGlobal
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function addGlobal(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }

    /**
     * Summary of addPath
     * @param string $namespace
     * @param string|null $path
     * @return void
     */
    public function addPath(string $namespace, string|null $path = null): void
    {
        $this->twig->getLoader()->addPath($path, $namespace);
    }

    /**
     * Summary of render
     * @param string $view
     * @param array|null $params
     * @return string
     */
    public function render(string $view, array|null $params = []): string
    {
        return $this->twig->render($view . '.html.twig', $params);
    }
}
