<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBanned
{
    public function handle(Request $request, Closure $next)
    {
        // Eximir a Administradores del bloqueo por IP si están autenticados
        if (Auth::check() && (Auth::user()->is_super_admin || Auth::user()->is_admin)) {
            return $next($request);
        }

        // Verificar IP Baneada
        $isIpBanned = \App\Models\Ban::where('ip_address', $request->ip())
            ->where(function($q) {
                $q->where('is_permanent', true)
                  ->orWhere('banned_until', '>', now());
            })->exists();

        if ($isIpBanned) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Tu dirección IP está bloqueada.'], 403);
            }
            abort(403, 'Tu dirección IP está bloqueada.');
        }
        if (Auth::check() && Auth::user()->isBanned('account')) {

            // Si es una petición de la API, bloquear inmediatamente sin excepciones
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Tu cuenta está bloqueada y no puedes realizar esta acción.'], 403);
            }

            // Lógica para vistas Blade clásicas (si restan)
            $allowedRoutes = ['profile.index', 'profile.edit', 'profile.update', 'logout', 'login', 'home'];
            $currentRoute = $request->route() ? $request->route()->getName() : null;
            
            if (!in_array($currentRoute, $allowedRoutes) && $currentRoute !== null) {
                return redirect()->route('profile.index')
                    ->with('error', 'Tu cuenta está baneada. No puedes realizar esta acción.');
            }
        }

        return $next($request);
    }
}
