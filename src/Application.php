<?php

namespace Shulha\Framework;

use Shulha\Framework\DI\Injector;
use Shulha\Framework\DI\Service;
use Shulha\Framework\Exception\ActionNotFoundException;
use Shulha\Framework\Exception\ConfigRoutesNotFoundException;
use Shulha\Framework\Exception\ConfigViewPathNotFoundException;
use Shulha\Framework\Exception\ControllerNotFoundException;
use Shulha\Framework\Renderer\RendererBlade;
use Shulha\Framework\Request\Request;
use Shulha\Framework\Response\Response;
use Shulha\Framework\Router\Route;
use Shulha\Framework\Router\Router;
use Auryn;

/**
 * Class Application
 * @package Shulha\Framework
 */
class Application
{
    /**
     * @var Auryn\Injector
     */
    private $injector;

    /**
     * Application config
     * @var array
     */
    public $config = [];

    /**
     * @var Request data
     */
    protected $request;

    /**
     * Application initialization
     * @param array $config
     */
    public function __construct($config = [])
    {
        require __DIR__ . "/../vendor/autoload.php";
        $this->config = $config;
        Injector::setConfig($this->config);
        $this->injector = new Auryn\Injector;
        $this->request = $this->injector->make(Injector::getInterface('Request'));
    }

    /**
     * Process the request
     */
    public function run()
    {
        try {
            if (empty($this->config['path_to_views']))
                throw new ConfigViewPathNotFoundException("Config With Path to Views Not Found");
            RendererBlade::$path_to_views = $this->config['path_to_views'];

            if (empty($this->config['router']['config']))
                throw new ConfigRoutesNotFoundException("Config With Routes Not Found");

            $this->injector->define(Injector::getInterface('Router'), [':config_route' => $this->config['router']['config']]);
            $router = $this->injector->make(Injector::getInterface('Router'));

            $route = $router->getRoute($this->request);
            if ($route) {
                $response = $this->useAuryn($route);
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        if ($response instanceof Response) {
            $response->send();
        }
    }

    /**
     * Process route
     *
     * @param Route $route
     * @return mixed
     * @throws ActionNotFoundException
     * @throws ControllerNotFoundException
     */
    protected function processRoute(Route $route)
    {
        $route_controller = $route->getController();
        $route_method = $route->getMethod();
        if (class_exists($route_controller)) {
            $rc = new \ReflectionClass($route_controller);
            if ($rc->hasMethod($route_method)) {
                $rm = $rc->getMethod($route_method);
                $rp = $rm->getParameters();
                $params = Injector::resolveParams($rp, $route->getParams());
                $controller = Injector::make($route_controller);
                return $rm->invokeArgs($controller, $params);
            } else {
                throw new ActionNotFoundException("Controller \"$route_controller\" has not \"$route_method\" Action");
            }
        } else {
            throw new ControllerNotFoundException("Controller \"$route_controller\" not found");
        }
    }

    /**
     * Process route using Auryn
     *
     * @param Route $route
     * @return mixed
     * @throws ActionNotFoundException
     * @throws ControllerNotFoundException
     */
    protected function useAuryn(Route $route)
    {
        $route_controller = $route->getController();
        $route_method = $route->getMethod();
        $params = [];

        if (class_exists($route_controller)) {

            foreach($route->getParams() as $k => $v){
                $n = ":" . $k;
                $params[$n] = $v;
            }
            $controller = $this->injector->make($route_controller);
            $callableController = array($controller, $route_method);

            if (!is_callable($callableController)) {
                throw new ActionNotFoundException("Controller \"$route_controller\" has not \"$route_method\" Action");
            } else {
                return $this->injector->execute($callableController, $args = $params);
            }
        } else {
            throw new ControllerNotFoundException("Controller \"$route_controller\" not found");
        }
    }

    /**
     * Close all
     */
    public function __destruct()
    {
    }

}