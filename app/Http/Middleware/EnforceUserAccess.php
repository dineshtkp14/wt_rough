<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnforceUserAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Invoice search is available to every authenticated user. Keep this
        // read-only search route independent from the account-lock gate.
        if ($user && $request->routeIs('customer.billno')) {
            return $next($request);
        }

        $isLocked = $user
            ? (int) DB::table('users')->where('id', $user->getAuthIdentifier())->value('is_locked') === 1
            : false;

        if ($user && $isLocked && !$user->isAdmin()) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('message', 'This is trial purpose only, no login. Buy from hostinger.com.');
        }

        return $next($request);
    }
}
