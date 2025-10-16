<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class isGuest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check() == false){
            return $next($request);
        } else {
            //jika sudah login
            if(Auth::user()->role == 'admin'){
                //jika admin ke halaman admin
                return redirect()->route('admin.dashboard');
            } else {
                //selain admin ke home
                return redirect()->route('home');
            }
        }
    }
}
