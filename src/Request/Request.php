<?php

namespace Shulha\Framework\Request;

/**
 * Class Request
 * @package Shulha\Framework\Request
 */
class Request implements RequestInterface
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
     * Variables of request
     * @var array
     */
    protected $requestVariables = [];

    /**
     * Extract headers
     */
    private function __construct()
    {
        $this->requestVariables += $_REQUEST;
        if ($json = json_decode(file_get_contents("php://input"), true))
            $this->requestVariables += $json;

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
        return key_exists($var, $this->requestVariables) ? $this->requestVariables[$var] : $default;
    }

}