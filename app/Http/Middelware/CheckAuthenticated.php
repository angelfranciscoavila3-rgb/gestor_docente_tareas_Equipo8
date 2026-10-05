<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthenticated
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        if (!$request->session()->has('usuario_id')) {
            return redirect()
                ->route('login')
                ->with('error', 'Debes iniciar sesión.');
        }

        return $next($request);
    }
}
