<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnforceUserAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user && $user->is_locked && !$user->isAdmin()) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('message', 'This is trial purpose only, no login. Buy from hostinger.com.');
        }

        return $next($request);
    }
}
