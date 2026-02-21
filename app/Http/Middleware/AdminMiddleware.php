<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        // Bootstrap mode: if admins are not configured yet, allow the first login
        // to open admin panel and set is_admin in users table.
        $hasAdmins = User::query()->where('is_admin', true)->exists();
        if ($user->is_admin || !$hasAdmins) {
            return $next($request);
        }

        abort(403);
    }
}
