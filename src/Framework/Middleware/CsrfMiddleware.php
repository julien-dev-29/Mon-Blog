<?php
namespace Framework\Middleware;

use ArrayAccess;
use Exception;
use Framework\Exception\CsrfInvalidException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function PHPUnit\Framework\throwException;

class CsrfMiddleware implements MiddlewareInterface
{
    /**
     * @var string
     */
    private $formKey;

    /**
     * @var string
     */
    private $sessionKey;

    /**
     * @var ArrayAccess
     */
    private $session;

    private $limit;

    public function __construct(&$session, int $limit = 50, string $formKey = '_csrf', string $sessionKey = 'csrf')
    {
        $this->validSession($session);
        $this->session = &$session;
        $this->formKey = $formKey;
        $this->sessionKey = $sessionKey;
        $this->limit = $limit;
    }

    /**
     * Summary of process
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $next
     * @return void
     */
    public function process(Request $request, RequestHandlerInterface $next): ResponseInterface
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE'])) {
            $params = $request->getParsedBody() ?: [];
            if (!array_key_exists($this->formKey, $params)) {
                $this->reject();
            } else {
                $csrfList = $this->session[$this->sessionKey] ?? [];
                if (in_array($params[$this->formKey], $csrfList)) {
                    $this->useToken($params[$this->formKey]);
                    return $next->handle($request);
                } else {
                    $this->reject();
                }
            }
        }
        return $next->handle($request);
    }

    /**
     * Génére un token
     *
     * @return string
     */
    public function generateToken(): string
    {
        $token = bin2hex(random_bytes(16));
        $csrfList = $this->session[$this->sessionKey] ?? [];
        $csrfList[] = $token;
        $this->session[$this->sessionKey] = $csrfList;
        return $token;
    }

    /**
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey;
    }

    /**
     * Lève une exception
     *
     * @throws \Framework\Exception\CsrfInvalidException
     * @return never
     */
    private function reject()
    {
        throw new CsrfInvalidException("Wesh!!!");
    }

    /**
     *
     * @param mixed $token
     * @return void
     */
    private function useToken($token)
    {
        $tokens = array_filter(
            array: $this->session[$this->sessionKey],
            callback: fn($t) => $token !== $t
        );
        $this->session[$this->sessionKey] = $tokens;
    }

    /**
     * Supprime le premier token du tableau quand la limite est atteinte
     *
     * @return void
     */
    private function limitTokens()
    {
        $tokens = $this->session[$this->sessionKey];
        if (count($tokens) > $this->limit) {
            array_shift($tokens);
        }
        $this->session[$this->sessionKey] = $tokens;
    }

    /**
     * Lève un exception si la session n'est ni une instance de ArrayAccess ni un tableau
     *
     * @param mixed $session
     * @throws \Exception
     * @return void
     */
    private function validSession($session)
    {
        if (!is_array($session) && !$session instanceof ArrayAccess) {
            throw new Exception('La session passée au middleware CSRF ne peut pas être traité comme tableau');
        }
    }
}
