<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Parent and Specialist portals
        if ($user->role === 'parent' || $user->role === 'specialist') {
            // They can't access the main dashboard. They have their own portals.
            // But we'll handle this in web.php by grouping dashboard routes and portal routes.
            // If they reach here, it's checked by the specific route middleware.
        }

        if (empty($roles)) {
            // Global checks if no specific roles passed
            $routeName = $request->route() ? $request->route()->getName() : '';

            if ($user->role === 'viewer') {
                if ($routeName !== 'reports.index' && $routeName !== 'logout' && $routeName !== 'login') {
                    abort(403, 'غير مصرح لك بالدخول. حسابك مشاهد للتقارير فقط.');
                }
            }

            if ($user->role === 'user') {
                if (str_starts_with($routeName, 'reports.') || str_starts_with($routeName, 'logs.') || str_starts_with($routeName, 'users.')) {
                    abort(403, 'غير مصرح لك بالدخول لهذه الصفحة.');
                }
            }
        } else {
            // Check specific roles passed to middleware
            if (!in_array($user->role, $roles)) {
                abort(403, 'غير مصرح لك بالدخول.');
            }
        }

        return $next($request);
    }
}
