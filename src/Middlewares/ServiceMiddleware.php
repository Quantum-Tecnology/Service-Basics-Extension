<?php

namespace \GustavoSantarosa\ServiceBasicsExtension\Middleware;

use Illuminate\Http\Request;

class ServiceMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, \Closure $next)
    {
        $request->route()->getController()->booted();

        return $next($request);
    }
}
