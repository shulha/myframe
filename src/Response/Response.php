<?php

namespace Shulha\Framework\Response;

/**
 * Class Response
 * @package Shulha\Framework\Response
 */
class Response
{
    /**
     * Response code
     * @var int
     */
    public $code = 200;

    /**
     * Status messages
     */
    const STATUS_MSGS = [
        '200' => 'Ok',
        '301' => 'Moved permanently',
        '302' => 'Moved temporary',
        '401' => 'Auth required',
        '403' => 'Access denied',
        '404' => 'Not found',
        '500' => 'Server error'
    ];

    /**
     * @var string
     */
    protected $body = '';

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * Response constructor.
     * @param $content
     * @param int $code
     */
    public function __construct($content, $code = 200)
    {
        $this->setContent($content);
        $this->code = $code;
        $this->addHeader('Content-Type', 'text/html');
    }

    /**
     * Set content
     * @param $content
     */
    public function setContent($content)
    {
        $this->body = $content;
    }

    /**
     * Add header
     * @param $key
     * @param $value
     */
    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * Send response
     */
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();
        exit();
    }

    /**
     * Send headers
     */
    public function sendHeaders()
    {
        header($_SERVER['SERVER_PROTOCOL'] . " " . $this->code . " " . self::STATUS_MSGS[$this->code]);
        if (!empty($this->headers)) {
            foreach ($this->headers as $key => $value) {
                header($key . ": " . $value);
            }
        }
    }

    /**
     * Send content
     */
    public function sendContent()
    {
        echo $this->body;
    }

}