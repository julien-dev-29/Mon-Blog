<?php

namespace Framework\Session;

class ArraySession implements SessionInterface
{
    /**
     * @var
     */
    private $session = [];

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function delete(string $key): void
    {
        unset($this->session[$key]);
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null): mixed
    {
        if (array_key_exists($key, $this->session)) {
            return $this->session[$key];
        } else {
            return $default;
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $this->session[$key] = $value;
    }
}
