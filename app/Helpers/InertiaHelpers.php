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
        return Inertia::render($component, array_merge([
            'auth' => [
                'user' => Auth::user(),
            ],
        ], $props));
    }
}
