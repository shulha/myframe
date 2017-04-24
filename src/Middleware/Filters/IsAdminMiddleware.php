<?php

namespace Shulha\Framework\Middleware\Filters;

use Closure;
use Shulha\Framework\Middleware\MiddlewareInterface;
use Shulha\Framework\Request\Request;
use Shulha\Framework\Response\RedirectResponse;

class IsAdminMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next, $role = null)
    {
        if ($request->getRequestVariable("admin") !== $role)
            return new RedirectResponse("/");
        return $next($request);
    }
}