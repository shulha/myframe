<?php

namespace Shulha\Framework\Renderer;

/**
 * Class Renderer
 * @package Shulha\Framework\Renderer
 */
class Renderer
{
    /**
     * @param string $view_path
     * @param array $params
     * @return string
     */
    public static function render(string $view_path, array $params = []): string
    {
        ob_start();

        extract($params);
        include $view_path;

        return ob_get_clean();
    }
}