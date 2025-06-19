<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Penghuni
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role == 'penghuni') {
            return $next($request);
        }

        // Redirect ke dashboard pemilik jika user adalah pemilik
        if (Auth::user()->role == 'pemilik') {
            return redirect()->route('dashboard.pemilik');
        }

        // Redirect ke login jika role tidak valid
        return redirect()->route('login');
    }
}
