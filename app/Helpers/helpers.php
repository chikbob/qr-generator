<?php

use App\Helpers\InertiaHelpers;

if (! function_exists('inertiaWithUser')) {
    /**
     * Render an Inertia component with authenticated user data
     */
    function inertiaWithUser(string $component, array $props = [])
    {
        return InertiaHelpers::inertiaWithUser($component, $props);
    }
}
