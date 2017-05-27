<?php

namespace Shulha\Framework\Middleware\Filters;

use Closure;
use Shulha\Framework\Middleware\Exception\NotPermittedException;
use Shulha\Framework\Middleware\MiddlewareInterface;
use Shulha\Framework\Request\Request;
use Shulha\Framework\Security\Security;

class CheckRoleMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next, $roleFirst = 'ADMIN', $roleSecond = null)
    {
        $user = Security::getUser();
        if(!in_array($roleFirst, $user->getRoles()))
            throw new NotPermittedException('You have no permissions to access this route');

        return $next($request);
    }
}