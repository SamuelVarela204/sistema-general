<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CajeroMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->hasRole('admin', 'gerente', 'cajero')) {
            return $next($request);
        }
        abort(403, 'No tienes permiso para acceder a esta sección.');
    }
}
