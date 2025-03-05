<?php

namespace Framework\Renderer;

interface RendererInterface
{
    /**
     * Permet d'ajouter un chemin pour charger les vues
     *
     * @param string $namespace
     * @param mixed $path
     * @return void
     */
    public function addPath(string $namespace, ?string $path = null): void;

    /**
     * Permet de rendre une vue
     * Le chemin peut être précisé avec des namespaces ajoutés via la méthode addPath()
     * $this->render('@blog/view');
     * $this->render('view');
     *
     * @param string $view
     * @param mixed $params
     * @return void
     */
    public function render(string $view, ?array $params = []): string;

    /**
     * Permet d'ajouter des variables globales pour toutes les vues
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function addGlobal(string $key, $value): void;
}
