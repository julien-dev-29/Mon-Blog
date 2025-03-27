<?php
namespace Framework\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MethodMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $next): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        if (array_key_exists(
            key: '_method',
            array: $parsedBody
        ) &&
            in_array(
                needle: $parsedBody['_method'],
                haystack: ['PUT', 'DELETE']
            )
        ) {
            $request = $request->withMethod($parsedBody['_method']);
        }
        return $next->handle($request);
    }
}
