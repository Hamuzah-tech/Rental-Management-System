<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user) {
            // Log the unauthenticated attempt
            Log::warning('Unauthenticated access attempt to protected route', [
                'ip' => $request->ip(),
                'path' => $request->path(),
                'roles_required' => $roles
            ]);

            abort(401, 'Unauthenticated. Please log in.');
        }

        $userRole = strtolower($user->role ?? '');
        $roles = array_map('strtolower', $roles);

        // Check if user has the required role
        if (!in_array($userRole, $roles, true)) {
            // Log the unauthorized attempt
            Log::warning('Unauthorized role access attempt', [
                'user_id' => $user->id,
                'user_role' => $userRole,
                'roles_required' => $roles,
                'ip' => $request->ip(),
                'path' => $request->path()
            ]);

            abort(403, 'Unauthorized access. You do not have the required role.');
        }

        // Check if user is active (if is_active field exists)
        if (isset($user->is_active) && !$user->is_active) {
            Log::warning('Inactive user attempted to access protected route', [
                'user_id' => $user->id,
                'user_role' => $userRole,
                'ip' => $request->ip(),
                'path' => $request->path()
            ]);

            abort(403, 'Your account is deactivated. Please contact support.');
        }

        return $next($request);
    }
}