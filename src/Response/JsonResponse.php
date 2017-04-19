<?php

namespace Shulha\Framework\Response;

/**
 * Class JsonResponse
 * @package Shulha\Framework\Response
 */
class JsonResponse extends Response
{
    /**
     * JsonResponse constructor.
     * @param $content
     * @param int $code
     */
    public function __construct($content, $code = 200)
    {
        parent::__construct($content, $code);
        $this->addHeader('Content-Type', 'application/json');
    }

    /**
     * Send content as JSON
     */
    public function sendContent()
    {
        echo json_encode($this->body);
    }
}