<?php

namespace Framework\Renderer;

class PHPRenderer implements RendererInterface
{
    /**
     * DEFAULT_NAMESPACE
     * @var string
     */
    public const DEFAULT_NAMESPACE = '__MAIN';

    /**
     * Summary of paths
     * @var array
     */
    private $paths = [];

    /**
     * Variables globalement accessible pout toutes les vues
     * @var array
     */
    private $globals = [];
    public function __construct(?string $defaultPath = null)
    {
        if ($defaultPath !== null) {
            $this->addPath(namespace: $defaultPath);
        }
    }

    /**
     * Permet d'ajouter un chemin pour charger les vues
     *
     * @param string $namespace
     * @param mixed $path
     * @return void
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        $path === null ?
            $this->paths[self::DEFAULT_NAMESPACE] = $namespace
            :
            $this->paths[$namespace] = $path;
    }

    /**
     * Permet de rendre une vue
     * Le chemin peut être précisé avec des namespaces ajoutés via la méthode addPath()
     * $this->render('@blog/view');
     * $this->render('view');
     *
     * @param string $view
     * @param mixed $params
     * @return bool|string
     */
    public function render(string $view, ?array $params = []): string
    {
        $this->hasNamespace($view) ?
            $path = $this->replaceNamespace($view) . '.php'
            :
            $path = $this->paths[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view . '.php';

        ob_start();
        $renderer = $this;
        extract($this->globals);
        extract($params);
        require $path;
        return ob_get_clean();
    }

    /**
     * Permet d'ajouter des variables globales pour toutes les vues
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function addGlobal(string $key, $value): void
    {
        $this->globals[$key] = $value;
    }

    /**
     * Envoie un booleen si il y a un namespace
     *
     * @param string $view
     * @return bool
     */
    private function hasNamespace(string $view): bool
    {
        return $view[0] === '@';
    }

    /**
     * Accède au namespace
     *
     * @param string $view
     * @return string
     */
    private function getNamespace(string $view): string
    {
        return substr($view, 1, strpos($view, '/') - 1);
    }

    /**
     * Remplace le symbol @ dans le chemin
     * @param string $view
     * @return string
     */
    private function replaceNamespace(string $view): string
    {
        $namespace = $this->getNamespace($view);
        return str_replace('@' . $namespace, $this->paths[$namespace], $view);
    }
}
