<?php

namespace Shulha\Framework\Router;

/**
 * Class Route
 * @package Shulha\Framework\Router
 */
class Route
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $controller;

    /**
     * @var string
     */
    private $method;

    /**
     * @var array
     */
    private $params = [];

    /**
     * Route constructor.
     * @param string $name
     * @param string $controller
     * @param string $method
     * @param array $params
     */
    public function __construct($name, $controller, $method, array $params = [])
    {
        $this->name = $name;
        $this->controller = $controller;
        $this->method = $method;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     */
    public function setController(string $controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method)
    {
        $this->method = $method;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }


}