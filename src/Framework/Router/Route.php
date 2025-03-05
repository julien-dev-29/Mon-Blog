<?php

namespace Framework\Router;

/**
 * Summary of Route
 */
class Route
{
    /**
     * Summary of name
     * @var string
     */
    private $name;

    /**
     * Summary of callable
     * @var callable
     */
    private $callable;

    /**
     * Summary of parameters
     * @var string[]
     */
    private $params;

    /**
     * Summary of __construct
     * @param string $name
     * @param string|callable $callable
     * @param array $parameters
     */
    public function __construct(string $name, $callable, array $params)
    {
        $this->name = $name;
        $this->callable = $callable;
        $this->params = $params;
    }
    /**
     * Summary of getName
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Summary of getCallback
     * @return string|callable
     */
    public function getCallback()
    {
        return $this->callable;
    }
    /**
     * Retrieve the URL parameters
     * @return string[]
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
