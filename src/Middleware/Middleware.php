<?php

namespace Shulha\Framework\Middleware;

use Closure;
use Shulha\Framework\Request\Request;

/**
 * Class Middleware
 * @package Shulha\Framework\Middleware
 */
class Middleware
{
    /**
     * Map of Middlewares
     * @var array
     */
    private $middlewaresMap = [
        'check_role' => 'Shulha\Framework\Middleware\Filters\IsAdminMiddleware',
        'check_token' => 'Shulha\Framework\Middleware\Filters\CheckTokenMiddleware'
    ];
    /**
     * Middlewares from Route
     * @var array
     */
    private $route_middlewares = [];

    /**
     * @var Request
     */
    private $request;

    /**
     * Middleware constructor.
     * @param Request $request
     * @param array $middlewaresMap
     * @param array $route_middlewares
     * @internal param array $middlewares
     */
    public function __construct(Request $request, array $middlewaresMap, array $route_middlewares)
    {
        $this->request = $request;
        $this->middlewaresMap += $middlewaresMap;
        $this->route_middlewares = $route_middlewares;
    }

    /**
     * Get next middleware
     * @param string $middlewareClassName
     * @param Closure $previousNext
     * @param array $params
     * @return Closure
     */
    function getNext(string $middlewareClassName, Closure $previousNext, array $params = null)
    {
        return function ($request) use ($middlewareClassName, $previousNext, $params) {

            if (!class_exists($middlewareClassName))
                return $previousNext;

            if (!is_subclass_of($middlewareClassName, 'Shulha\Framework\Middleware\MiddlewareInterface')) {
                throw new \Exception("Class \"$middlewareClassName\" don't implements MiddlewareInterface");
            }
            $middlewareClass = new $middlewareClassName();

            return call_user_func_array(
                [$middlewareClass, 'handle'],
                array_merge([$request, $previousNext], $params)
            );
        };
    }

    /**
     * Start filtering of request
     * @param $response
     * @return mixed
     */
    public function filtering($response)
    {
        $request = $this->request;

        $next = function ($request) use ($response) {
            return $response;
        };

        foreach (array_reverse($this->route_middlewares) as $middleware) {
            $middlewareData = explode(":", $middleware);
            $middlewareName = $middlewareData[0];

            $middlewareParams = [];

            if (count($middlewareData) > 1) {
                $middlewareParams = explode(",", $middlewareData[1]);
            }

            if (array_key_exists($middlewareName, $this->middlewaresMap)) {
                $middlewareClassName = $this->middlewaresMap[$middlewareName];
                $next = $this->getNext($middlewareClassName, $next, $middlewareParams);
            }
        }

        return $next($request);
    }
}