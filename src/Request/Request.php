<?php

namespace Shulha\Framework\Request;

/**
 * Class Request
 * @package Shulha\Framework\Request
 */
class Request
{
    /**
     * Request headers
     * @var array
     */
    protected $headers = [];

    /**
     * Array of upload files
     * @var array
     */
    public $uploaded_array = [];

    /**
     * @var string
     */
    public $errormsg = '';

    /**
     * Variables of request
     * @var array
     */
    protected $requestVariables = [];

    /**
     * Extract headers
     */
    public function __construct()
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
     * Determine if the uploaded data contains a file.
     *
     * @param $file
     * @return bool
     */
    public function hasFile($file)
    {
        if (key_exists($file, $_FILES))
            return true;

        return false;
    }

    /**
     * Upload files from request
     *
     * @param null $keys
     * @param string $root
     * @param string $folder
     * @return bool
     * @throws \Exception
     */
    public function uploadFiles($keys = null, $folder = "/tmp/", $root = null)
    {
        if (!$root) $root = $_SERVER['DOCUMENT_ROOT'];

        foreach ($_FILES[$keys]["error"] as $key => $error)
        {
            $tmp_name = $_FILES[$keys]["tmp_name"][$key];
            if (!$tmp_name) continue;

            $name = uniqid();

            if ($error == UPLOAD_ERR_OK)
            {
                if ( move_uploaded_file($tmp_name, $root.$folder.$name) ) {
                    $this->uploaded_array[] .= $folder.$name;
                }
                else
                    $this->errormsg .= "Could not move uploaded file '".$tmp_name."' to '".$name."'<br/>\n";
            }
            else $this->errormsg .= "Upload error. [".$error."] on file '".$name."'<br/>\n";
        }

        if ($this->errormsg) throw new \Exception($this->errormsg);

        return true;
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