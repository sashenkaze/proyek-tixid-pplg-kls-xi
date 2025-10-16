<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class isAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role == 'admin') {
            return $next($request);
            //melanjutkan proses ke halaman selanjutnya^^^
            //untuk staff
        } elseif (Auth::check() && Auth::user()->role = 'staff') {
            return redirect()->route('staff.promos.index');
        } else {
            //jika bukan admin, redirect ke halaman home
            return redirect()->route('home');
        }
    }
}
