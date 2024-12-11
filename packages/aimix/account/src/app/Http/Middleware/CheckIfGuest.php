<?php

namespace Aimix\Account\app\Http\Middleware;

use Closure;

class CheckIfGuest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      if(!\Auth::user()) {
        return redirect('/login');
      }
      
      return $next($request);
    }
}
