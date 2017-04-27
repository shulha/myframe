<?php

namespace Shulha\Framework\Middleware;

use Closure;
use Shulha\Framework\Request\Request;

interface MiddlewareInterface
{
    public function handle(Request $request, Closure $next);
}