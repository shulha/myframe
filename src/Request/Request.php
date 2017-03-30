<?php

namespace Shulha\Framework\Request;

/**
 * Class Request
 * @package Shulha\Framework\Request
 */
class Request
{
    /**
     * Request instance
     * @var null
     */
    private static $instance = null;

    /**
     * Request headers
     * @var array
     */
    protected $headers = [];

    /**
     * Extract headers
     */
    private function __construct()
    {
        if (function_exists('getallheaders'))
            $this->headers = getallheaders();
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_')
                $this->headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
    }

    /**
     * Request clone.
     */
    private function __clone()
    {
    }

    /**
     * Get Instance
     * @return Request
     */
    public static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get URI
     * @return string
     */
    public function getUri(): string
    {
        $uri = explode('?', $_SERVER["REQUEST_URI"]);
        return array_shift($uri);
    }

    /**
     * Get method
     * @return string
     */
    public function getMethod(): string
    {
        return $_SERVER["REQUEST_METHOD"];
    }

    /**
     * Get header by name or all headers by default
     * @param null $name
     * @return mixed
     */
    public function getHeader($name = null)
    {
        if (empty($name))
            return $this->headers;
        return isset($this->headers[$name]) ? $this->headers[$name] : null;
    }

    /**
     * Get variable of request
     * @param $var
     * @param null $default
     * @return null
     */
    public function getRequestVariable($var, $default = null)
    {
        if ($arr = json_decode(file_get_contents("php://input"), true))
            return !empty($arr[$var]) ? $arr[$var] : $default;

        return key_exists($var, $_REQUEST) ? $_REQUEST[$var] : $default;
    }

    /**
     * Returns true if received Ajax-request and assumed to response back json
     *
     * @return bool
     */
    public function wantsJson(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    public function __get($name)
    {
        return isset($_REQUEST[$name]) ? $_REQUEST[$name] : null;
    }

    public function __isset($name)
    {
        return isset($_REQUEST[$name]);
    }

}