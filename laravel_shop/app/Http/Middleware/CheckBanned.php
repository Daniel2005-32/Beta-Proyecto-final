<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBanned
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->isBanned()) {
            // Permitir acceso solo a profile, logout y rutas de autenticación
            $allowedRoutes = [
                'profile.index',
                'profile.edit',
                'profile.update',
                'profile.password',
                'profile.avatar',
                'logout',
                'login',
                'home'
            ];
            
            $currentRoute = $request->route() ? $request->route()->getName() : null;
            
            // Si la ruta actual no está en las permitidas, redirigir al perfil con mensaje
            if (!in_array($currentRoute, $allowedRoutes) && $currentRoute !== null) {
                return redirect()->route('profile.index')
                    ->with('error', 'Tu cuenta está baneada. No puedes realizar esta acción.');
            }
        }

        return $next($request);
    }
}
