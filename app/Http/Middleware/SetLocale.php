<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $lang = $request->cookie('lang');
        if ($lang) {
            app()->setLocale($lang);
        }

        return $next($request);
    }
}
