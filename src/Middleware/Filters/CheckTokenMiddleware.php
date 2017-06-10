<?php

namespace Shulha\Framework\Middleware\Filters;

use Shulha\Framework\DI\Service;
use Shulha\Framework\Middleware\Exception\NotPermittedException;
use Shulha\Framework\Middleware\MiddlewareInterface;
use Shulha\Framework\Request\Request;

class CheckTokenMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, \Closure $next=null, $valueToken = null, $paramToken = null)
    {
        $session = Service::get('injector')->make('Shulha\Framework\Session\Session');

        if ($request->token !== $session->token) {
            throw new NotPermittedException('You have not or invalid token');
        }
        return $next($request);
    }
}