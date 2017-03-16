<?php

namespace Shulha\Framework;

use Shulha\Framework\Exception\ActionNotFoundException;
use Shulha\Framework\Exception\ConfigRoutesNotFoundException;
use Shulha\Framework\Exception\ControllerNotFoundException;
use Shulha\Framework\Request\Request;
use Shulha\Framework\Response\Response;
use Shulha\Framework\Router\Router;

/**
 * Class Application
 * @package Shulha\Framework
 */
class Application
{
    /**
     * Application config
     * @var array
     */
    public $config = [];

    /**
     * Application initialization
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * Process the request
     */
    public function run()
    {
        try {
            if (empty($this->config['routes']))
                throw new ConfigRoutesNotFoundException("Config With Routes Not Found");
            $router = new Router($this->config['routes']);

            $route = $router->getRoute(Request::getInstance());
            $route_controller = $route->getController();
            $route_method = $route->getMethod();

            if (class_exists($route_controller)) {
                $rc = new \ReflectionClass($route_controller);
                if ($rc->hasMethod($route_method)) {
                    $rm = $rc->getMethod($route_method);
                    $controller = $rc->newInstance();
                    $response = $rm->invokeArgs($controller, $route->getParams());

                    if ($response instanceof Response) {
                        $response->send();
                    }
                } else {
                    throw new ActionNotFoundException("Controller \"$route_controller\" has not \"$route_method\" Action");
                }
            } else {
                throw new ControllerNotFoundException("Controller \"$route_controller\" not found");
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }

    }

    /**
     * Close all
     */
    public function __destruct()
    {
    }
}