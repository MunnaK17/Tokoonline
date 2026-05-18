<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && (int) auth()->user()->role === 2) {
            return $next($request);
        }

        return redirect()->route('frontend.login')->with('error', 'Anda harus login sebagai customer');
    }
}
