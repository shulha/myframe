<?php

namespace Shulha\Framework\Controller;

use Shulha\Framework\Renderer\RendererBlade;
use Shulha\Framework\Response\Response;

/**
 * Class Controller
 * @package Shulha\Framework\Controller
 */
class Controller
{
    /**
     * @param string $view_name
     * @param array $params
     * @return Response
     */
    public function render(string $view_name, array $params = []): Response
    {
        return new Response(RendererBlade::render($view_name, $params));
    }

}