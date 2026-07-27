<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('landlord.login');
        }

        $userRole = strtolower($user->role ?? '');
        $roles = array_map('strtolower', $roles);

        if (!in_array($userRole, $roles)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}