<?php

namespace Shulha\Framework;

use Shulha\Framework\Exception\ActionNotFoundException;
use Shulha\Framework\Exception\ConfigRoutesNotFoundException;
use Shulha\Framework\Exception\ConfigViewPathNotFoundException;
use Shulha\Framework\Exception\ControllerNotFoundException;
use Shulha\Framework\Renderer\RendererBlade;
use Shulha\Framework\Request\Request;
use Shulha\Framework\Response\JsonResponse;
use Shulha\Framework\Response\Response;
use Shulha\Framework\Router\Exception\RouteNotFoundException;
use Shulha\Framework\Router\Route;
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
        RendererBlade::$path_to_views[] = dirname(__FILE__) . '/Views';
    }

    /**
     * Process the request
     */
    public function run()
    {
        try {
            if (empty($this->config['path_to_views']))
                throw new ConfigViewPathNotFoundException("Config With Path to Views Not Found");
            RendererBlade::$path_to_views[] = $this->config['path_to_views'];

            if (empty($this->config['routes']))
                throw new ConfigRoutesNotFoundException("Config With Routes Not Found");
            $router = new Router($this->config['routes']);

            $route = $router->getRoute($this->request);

            if($route){
                $response = $this->processRoute($route);
            }
        } catch (RouteNotFoundException $e) {
            $response = $this->setError($e->getMessage(), 404);
        } catch (\Exception $e) {
            $response = $this->setError($e->getMessage(), 500);
        }

        $this->prepareResponse($response)->send();
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
                $method_params = [];
                foreach ($rp as $param) {
                    if (key_exists($param->getName(), $route->getParams()))
                        $method_params[$param->getName()] = $route->getParams()[$param->getName()];
                    if (!empty($param->getClass()) and $param->getClass()->getName() == 'Shulha\Framework\Request\Request')
                        $method_params[$param->getName()] = Request::getInstance();
                }
                $result = ($method_params) ? $method_params : $route->getParams();
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
     * Prepare content to be processed like response
     *
     * @param   $content
     * @return  Response
     */
    protected function prepareResponse($content):Response
    {
        if($content instanceof Response){
            // Do nothing, just return:
            return $content;
        }

        // Otherwise...
        if($this->request->wantsJson() || is_array($content) || is_object($content)){
            // Deal with Json response:
            $response = new JsonResponse($content);
        } else {
            $response = new Response($content);
        }

        return $response;
    }

    /**
     * Create system error response
     *
     * @param $message
     * @return mixed
     */
    public function setError($message, $code = 500)
    {
        if($this->request->wantsJson()){
            return compact('code', 'message');
        } else {
            return RendererBlade::render('error.'.$code, compact('message'));
        }
    }

    /**
     * Close all
     */
    public function __destruct()
    {
    }
}