<?php

namespace Framework\Session;

class FlashService
{
    private $session;
    private $sessionKey = 'flash';
    private $messages;
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function success(string $message)
    {
        $flash = $this->session->get($this->sessionKey, []);
        $flash['success'] = $message;
        $this->session->set($this->sessionKey, $flash);
    }

    public function error(string $message)
    {
        $flash = $this->session->get($this->sessionKey, []);
        $flash['error'] = $message;
        $this->session->set($this->sessionKey, $flash);
    }

    /**
     * Summary of get
     * @param string $type
     */
    public function get(string $type): ?string
    {
        if ($this->messages === null) {
            $this->messages = $this->session->get($this->sessionKey, []);
            $this->session->delete($this->sessionKey);
        }
        if (array_key_exists($type, $this->messages)) {
            return $this->messages[$type];
        }
        return null;
    }
}
