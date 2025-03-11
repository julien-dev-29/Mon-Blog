<?php

namespace Framework\Actions;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Trait RouterAction
 * @package Framework\Actions
 */
trait RouterAction
{
    /**
     * Renvoie une réponse de redirection
     *
     * @param string $path
     * @param array $params
     * @return ResponseInterface
     */
    public function redirect(string $path, array $params = []): ResponseInterface
    {
        $redirectURL = $this->router->generateURL($path, $params);
        return new Response()
            ->withStatus(301)
            ->withHeader('Location', $redirectURL);
    }
}
