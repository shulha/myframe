<?php

namespace Shulha\Framework\Router;

use Shulha\Framework\Request\Request;
use Shulha\Framework\Router\Exception\EmptyActionConfigRoutesException;
use Shulha\Framework\Router\Exception\InvalidRouteNameException;
use Shulha\Framework\Router\Exception\NoControllerMethodException;
use Shulha\Framework\Router\Exception\RouteKeyNotPassedException;
use Shulha\Framework\Router\Exception\RouteNotFoundException;

/**
 * Class Router
 * @package Shulha\Framework\Router
 */
class Router
{
    const DEFAULT_REGEXP = "[^\/]+";

    /**
     * Routing map
     * @var array
     */
    private $routes = [];

    /**
     * @var Request instance
     */
    private $request;

    /**
     * Router constructor.
     * @param array $config_route
     * @param Request $request
     */
    public function __construct(array $config_route, Request $request)
    {
        $this->request = $request;

        foreach ($config_route as $key => $value) {
            $existed_variables = $this->getExistedVariables($value);
            $this->routes[$key] = [
                "origin" => $value["pattern"],
                "regexp" => $this->getRegexpFromRoute($value, $existed_variables),
                "method" => isset($value["method"]) ? $value["method"] : "GET",
                "controller_name" => $this->getControllerName($value),
                "controller_method" => $this->getControllerMethod($value),
                "variables" => $existed_variables,
                "middlewares" => $value["middlewares"] ?? [],
            ];
        }
    }

    /**
     * Get route object
     * @return Route
     * @throws RouteNotFoundException
     * @internal param Request $request
     */
    public function getRoute(): Route
    {
        $method = $this->request->getMethod();
        $uri = $this->request->getUri();

        foreach ($this->routes as $name => $value) {
            if (($value['method'] == $method) and (preg_match('/' . $value['regexp'] . '/', $uri, $matches))) {
                $params = array_combine($value['variables'], array_slice($matches, 1));
                return new Route($name, $value['controller_name'], $value['controller_method'], $params, $value["middlewares"]);
            }
        }
        throw new RouteNotFoundException("Route not found!");
    }

    /**
     * Get name of controller
     * @param array $config_route
     * @return string
     * @throws EmptyActionConfigRoutesException
     */
    private function getControllerName(array $config_route): string
    {
        if (empty($config_route["action"]))
            throw new EmptyActionConfigRoutesException("Empty Action In Config Routes -- No Controller Name");

        return explode("@", $config_route["action"])[0];
    }

    /**
     * Get controller method
     * @param array $config_route
     * @return string
     * @throws NoControllerMethodException
     */
    private function getControllerMethod(array $config_route): string
    {
        if (stripos($config_route["action"], '@') === false)
            throw new NoControllerMethodException("No Controller Action");

        return explode("@", $config_route["action"])[1];
    }

    /**
     * Get regexp by config
     * @param array $config_route
     * @param array $existed_variables
     * @return string
     */
    private function getRegexpFromRoute(array $config_route, array $existed_variables): string
    {
        $pattern = $config_route["pattern"];
        $result = str_replace("/", "\/", $pattern);
        $variables_names = $existed_variables;
        for ($i = 0; $i < count($variables_names); $i++) {
            $var_reg =
                "(" .
                (array_key_exists($variables_names[$i], $config_route["variables"])
                    ? $config_route["variables"][$variables_names[$i]]
                    : self::DEFAULT_REGEXP)
                . ")";
            $result = str_replace("{" . $variables_names[$i] . "}", $var_reg, $result);
        }
        return "^" . $result . "$";
    }

    /**
     * Get variables from config pattern
     * @param $config_route
     * @return array
     */
    private function getExistedVariables($config_route)
    {
        preg_match_all("/{.+}/U", $config_route["pattern"], $variables);
        return array_map(function ($value) {
            return substr($value, 1, strlen($value) - 2);
        }, $variables[0]);
    }

    /**
     * Get link
     * @param string $route_name
     * @param array $params
     * @return string
     * @throws InvalidRouteNameException
     * @throws RouteKeyNotPassedException
     */
    public function getLink(string $route_name, array $params = []): string
    {
        if (!array_key_exists($route_name, $this->routes))
            throw new InvalidRouteNameException("\"$route_name\" route was not found in config");

        preg_match_all("/\{([\w\d_]+)\}/", $link = $this->routes[$route_name]['origin'], $matches);
        foreach ($matches[1] as $key) {
            if (!array_key_exists($key, $params))
                throw new RouteKeyNotPassedException("Key \"$key\" is required for route \"$route_name\"");
            $link = str_replace("{" . $key . "}", $params[$key], $link);
        }

        return $link;
    }
}