<?php

namespace Shulha\Framework\Response;

/**
 * Class RedirectResponse
 * @package Shulha\Framework\Response
 */
class RedirectResponse extends Response
{

    /**
     * RedirectResponse constructor.
     * @param $redirect_uri
     * @param int $code
     */
    public function __construct($redirect_uri, $code = 301)
    {
        $this->code = $code;
        $this->addHeader('Location', $redirect_uri);
    }

    /**
     * Send empty content
     */
    public function sendContent()
    {
    }
}