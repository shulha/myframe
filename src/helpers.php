<?php

use Shulha\Framework\DI\Service;
use Shulha\Framework\Renderer\RendererBlade;

if (!function_exists('debug')) {
    /**
     * Print the passed variables.
     *
     * @param  mixed
     * @return void
     */
    function debug($obj)
    {
        echo '<pre>';
            print_r($obj);
        echo '</pre>';
    }
}

if(!function_exists('view')){
    /**
     * Render view
     *
     * @param $view_name
     * @param array $data
     * @return mixed
     * @internal param bool $main_layout
     *
     */
    function view($view_name, $data = [])
    {
        $request = Service::get('Request');
        if($request->wantsJson()){
            // No need to render, just return raw data
            return empty($data) ? true : $data;
        }

        $output = RendererBlade::render($view_name, $data);

        return $output;
    }
}

if(!function_exists('route')){
    /**
     * Build route
     *
     * @param $route_name
     * @param array $params
     *
     * @return string
     */
    function route($route_name, $params = [])
    {
        $router = Service::get('Router');
        return $router->getLink($route_name, $params);
    }
}