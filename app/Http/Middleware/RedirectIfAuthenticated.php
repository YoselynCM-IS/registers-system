<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            // return redirect(RouteServiceProvider::HOME);
            if(auth()->user()->role == 'administrator'){
                return redirect()->route('administrator.folios');
            }
            if(auth()->user()->role == 'manager'){
                return redirect()->route('manager.files');
            }
            if(auth()->user()->role == 'reviewer'){
                return redirect()->route('reviewer.registers');
            }
            if(auth()->user()->role == 'capturist'){
                return redirect()->route('capturist.registers');
            }
        }

        return $next($request);
    }
}
