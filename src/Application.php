<?php

namespace Shulha\Framework;

use Shulha\Framework\Exception\ActionNotFoundException;
use Shulha\Framework\Exception\ConfigRoutesNotFoundException;
use Shulha\Framework\Exception\ConfigViewPathNotFoundException;
use Shulha\Framework\Exception\ControllerNotFoundException;
use Shulha\Framework\Renderer\RendererBlade;
use Shulha\Framework\Request\Request;
use Shulha\Framework\Response\Response;
use Shulha\Framework\Router\Route;
use Shulha\Framework\Router\Router;

/**
 * Class Application
 * @package Shulha\Framework
 */
class Application
{
    /**
     * Interface contracts map
     * @var array
     */
    private $contracts = [
        'Shulha\\Framework\\Request\\RequestInterface' => ['className' => 'Shulha\Framework\Request\Request', 'is_singleton' => true],
    ];


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
        require '../vendor/autoload.php';
        $this->config = $config;
        $this->request = Request::getInstance();
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

            if (empty($this->config['routes']))
                throw new ConfigRoutesNotFoundException("Config With Routes Not Found");
            $router = new Router($this->config['routes']);

            $route = $router->getRoute($this->request);
            if ($route) {
                $response = $this->processRoute($route);
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
                $result = $this->resolve($route, $rp);
                $controller = $rc->newInstance();
                return $rm->invokeArgs($controller, $result);
            } else {
                throw new ActionNotFoundException("Controller \"$route_controller\" has not \"$route_method\" Action");
            }
        } else {
            throw new ControllerNotFoundException("Controller \"$route_controller\" not found");
        }
    }

    /**
     * @param Route $route
     * @param $reflectionParameters
     * @return array
     * @throws \Exception
     */
    protected function resolve(Route $route, $reflectionParameters): array
    {
        $method_params = [];
        foreach ($reflectionParameters as $param) {
            if (key_exists($param->getName(), $route->getParams()))
                $method_params[$param->getName()] = $route->getParams()[$param->getName()];
            if (!empty($param->getClass())) {
                if (interface_exists($param->getClass()->getName())) {
                    $key = $param->getClass()->getName();
                    if (key_exists($key, $this->contracts)) {
                        if ($this->contracts[$key]['is_singleton'])
                            $method_params[$param->getName()] = $this->contracts[$key]['className']::getInstance();
                        else
                            $method_params[$param->getName()] = new $this->contracts[$key]['className']();
                            } else {
                        throw new \Exception("$key not found in contracts");
                    }
                } else {
                    throw new \Exception("Interface does not exists");
                }
            }
        }
        return ($method_params) ? $method_params : $route->getParams();
    }

    /**
     * Close all
     */
    public function __destruct()
    {
    }

}