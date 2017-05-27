<?php

namespace Shulha\Framework\Controller;

use Shulha\Framework\Renderer\RendererBlade;
use Shulha\Framework\Response\RedirectResponse;

/**
 * Class Controller
 * @package Shulha\Framework\Controller
 */
abstract class Controller
{
    /**
     * Render view
     *
     * @param string $view_name
     * @param array $params
     * @return string
     */
    public function render(string $view_name, array $params = []): string
    {
        return RendererBlade::render($view_name, $params);
    }

    /**
     * Redirect to route
     *
     * @param string    $route
     * @param array     Route params
     *
     * @return RedirectResponse
     */
    public function redirect($route = 'root', $params = []): RedirectResponse
    {
        return new RedirectResponse(route($route, $params));
    }

}