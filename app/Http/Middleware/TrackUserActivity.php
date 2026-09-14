<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TrackUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && Schema::hasColumn('users', 'last_activity_at')) {
            $user = auth()->user();
            if (!$user->last_activity_at || $user->last_activity_at->lt(now()->subSeconds(30))) {
                $updates = ['last_activity_at' => now()];
                if (Schema::hasColumn('users', 'last_login_at') && !$user->last_login_at) {
                    $updates['last_login_at'] = now();
                }
                $user->forceFill($updates)->saveQuietly();
            }
        }

        return $next($request);
    }
}
