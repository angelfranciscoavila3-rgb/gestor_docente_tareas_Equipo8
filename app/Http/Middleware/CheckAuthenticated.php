<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('auth_user_id')) {
            return redirect()
                ->route('login')
                ->with('error', 'Debes iniciar sesión para acceder.');
        }

        return $next($request);
    }
}
