<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBannedActions
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
            return redirect()->back()->with('error', 'No puedes realizar esta acción mientras estás baneado.');
        }

        return $next($request);
    }
}
