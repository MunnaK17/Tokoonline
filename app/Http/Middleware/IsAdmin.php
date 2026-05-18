<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && in_array((int) auth()->user()->role, [0, 1], true)) {
            return $next($request);
        }

        return redirect()->route('beranda')->with('error', 'Anda tidak berhak mengakses halaman admin.');
    }
}
