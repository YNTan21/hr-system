<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->is_admin !== 1) {
            return redirect()->route('user.dashboard');
        }

        return $next($request);
    }
} 