<?php

namespace Shulha\Framework\Controller;

use Shulha\Framework\Renderer\RendererBlade;

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

}