<?php

namespace Shulha\Framework\Middleware\Filters;

use Shulha\Framework\Middleware\MiddlewareInterface;
use Shulha\Framework\Request\Request;

class CheckTokenMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, \Closure $next=null, $valueToken = null, $paramToken = null)
    {
        $request = $request->getRequestVariable($valueToken);
        if (!isset($request) or $request !== $paramToken) {
            throw new \Exception('Invalid TOKEN');
        }
        return $next($request);
    }
}