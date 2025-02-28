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
     * @param callable $callable
     * @param array $parameters
     */
    public function __construct(string $name, callable $callable, array $params)
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
     * @return callable
     */
    public function getCallback(): callable
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
