<?php

namespace App\Helpers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class InertiaHelpers
{
    /**
     * Render an Inertia component with authenticated user data
     */
    public static function inertiaWithUser(string $component, array $props = [])
    {
        $user = Auth::user();

        return Inertia::render($component, array_merge([
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'plan_id' => $user->plan_id,
                    'is_admin' => (bool) $user->is_admin,
                ] : null,
            ],
        ], $props));
    }
}
