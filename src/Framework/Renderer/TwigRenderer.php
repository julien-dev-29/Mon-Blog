<?php

namespace Framework\Renderer;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRenderer implements RendererInterface
{
    private $twig;
    private $loader;
    public function __construct(FilesystemLoader $loader, Environment $twig)
    {
        $this->loader = $loader;
        $this->twig = $twig;
    }
    public function addGlobal(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }
    public function addPath(string $namespace, string|null $path = null): void
    {
        $this->loader->addPath($path, $namespace);
    }
    public function render(string $view, array|null $params = []): string
    {
        return $this->twig->render($view . '.html.twig', $params);
    }
}
