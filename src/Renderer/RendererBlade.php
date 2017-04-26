<?php

namespace Shulha\Framework\Renderer;

use \Illuminate\Container\Container;
use \Illuminate\Events\Dispatcher;
use \Illuminate\Filesystem\Filesystem;
use \Illuminate\View\View;
use \Illuminate\View\Factory;
use \Illuminate\View\FileViewFinder;
use \Illuminate\View\Compilers\BladeCompiler;
use \Illuminate\View\Engines\CompilerEngine;
use \Illuminate\View\Engines\PhpEngine;
use \Illuminate\View\Engines\EngineResolver;

/**
 * Class RendererBlade
 * @package Shulha\Framework\Renderer
 */
class RendererBlade extends Renderer
{
    /**
     * RendererBlade instance
     * @var null
     */
    private static $instance = null;

    public static $path_to_views = [];

    protected $path_to_cache;

    /**
     * RendererBlade constructor.
     */
    private function __construct()
    {
        $this->path_to_cache = (isset(self::$path_to_views[1])) ? (self::$path_to_views[1] . '/_cache') : (self::$path_to_views[0] . '/_cache');
        if (!is_dir($this->path_to_cache)) {
            if (!mkdir($this->path_to_cache)) {
                die('Не удалось создать директории...');
            }
        }
    }

    /**
     * RendererBlade clone
     */
    private function __clone()
    {
    }

    /**
     * Get Instance
     * @return Factory
     */
    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = (new self)->boot();
        }
        return static::$instance;
    }

    /**
     * @param string $view_name
     * @param array $params
     * @return string
     */
    public static function render(string $view_name, array $params = []): string
    {
        ob_start();

        echo self::getInstance()->make($view_name, $params);

        return ob_get_clean();
    }

    /**
     * Get the view factory instance
     * @return Factory
     */
    protected function boot()
    {
        $event = new Dispatcher(new Container);

        $filesystem = new FileSystem();

        $finder = new FileViewFinder($filesystem, self::$path_to_views);

        $engine = new CompilerEngine(
            new BladeCompiler($filesystem, $this->path_to_cache)
        );

        $resolver = new EngineResolver;

        $resolver->register('blade', function () use ($engine) {
            return $engine;
        });

        $resolver->register('php', function () {
            return new PhpEngine;
        });

        $factory = new Factory($resolver, $finder, $event);

        $view = new View($factory, $engine, '', '');

        return $view->getFactory();
    }

}