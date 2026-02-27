<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBannedActions
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->isBanned()) {
            return redirect()->back()->with('error', 'No puedes realizar esta acción mientras estás baneado.');
        }

        return $next($request);
    }
}
