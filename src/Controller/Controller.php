<?php

namespace Shulha\Framework\Controller;

use Shulha\Framework\Renderer\Renderer;
use Shulha\Framework\Response\Response;

/**
 * Class Controller
 * @package Shulha\Framework\Controller
 */
class Controller
{
    /**
     * path to layout
     * @var $main_layout
     */
    protected static $main_layout;

    /**
     * @param mixed $main_layout
     */
    public static function setMainLayout($main_layout = '')
    {
        self::$main_layout = $main_layout;
    }

    /**
     * @param string $view_path
     * @param array $params
     * @param bool $with_layout
     * @return Response
     */
    public function render(string $view_path, array $params = [], bool $with_layout = true): Response
    {
        $content = Renderer::render($view_path, $params);

        if ($with_layout) {
            if (!self::$main_layout) {
                self::$main_layout = pathinfo($view_path)['dirname'] . DIRECTORY_SEPARATOR . 'layout.html.php';
            }
            $content = Renderer::render(self::$main_layout, ['content' => $content]);
        }

        return new Response($content);
    }
}