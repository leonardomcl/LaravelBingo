<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Verifica se o usuário está logado E se a role dele é admin
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        // Se não for admin, redireciona para a home ou exibe erro 403
        abort(403, 'Acesso restrito ao Papai Noel!');
    }
}
