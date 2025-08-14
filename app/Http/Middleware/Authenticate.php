<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    // protected function redirectTo(Request $request)
    // {
        // return $request->expectsJson() ? null : route('login');
        // if (! $request->expectsJson()) {
        //     return redirect('/');
        // }
        // if(!Auth::guard('web')->check())
        // {
        //     return redirect('/');
        // }
        // elseif(!Auth::guard('member')->check())
        // {
        //     return redirect('/');
        // }
 
    // }
    protected function redirectTo(Request $request): ?string
    {
        // return $request->expectsJson() ? null : route('admin.login');
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    protected function authenticate($request, array $guards)
    {
        if ($this->auth->guard('web')->check()) {
            return $this->auth->shouldUse('web');
        }elseif($this->auth->guard('member')->check()){
            return $this->auth->shouldUse('member');
        }

        $this->unauthenticated($request, ['web', 'member']);
    }
}
