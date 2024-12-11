<?php

namespace App\Http\Middleware;
use App;
use Closure;
use App\Http\Middleware\Session;
use Request;

use \Backpack\LangFileManager\app\Models\Language;

class Localization
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
	    if($request->method() == 'POST'){
		    return $next($request);
			}
			
	    $current = $request->path();
	    $segment_one = $request->segment(1);
			$locale = \Session::has('lang')? \Session::get('lang'): (\App::getLocale()? \App::getLocale(): 'ru');
			
		if($request->getRequestUri() === '/') {
			\Session::put('lang', 'ru');
			\App::setLocale('ru');
			return $next($request);
		} elseif($request->getRequestUri() === '/ru') {
			abort(404);
			// \Session::put('lang', $segment_one);
			// \App::setLocale($segment_one);
			// return redirect(url('/'), 301);
		} elseif($segment_one == 'ru' || $segment_one == 'uk'){
			\Session::put('lang', $segment_one);
			\App::setLocale($segment_one);
			return $next($request);
		}else {
			abort(404);
			// return redirect($locale . $request->getRequestUri(), 301);
		}
		
/*
	    if(strlen($segment_one) == 2){
			
			$is_set = Language::where('abbr', $segment_one)->first();
			
			if($is_set) {
				\Session::put('lang', $segment_one);
			    \App::setLocale($segment_one);
			    
			    return $next($request);
		    }else{
			    
			    return redirect($locale.substr($request->getRequestUri(),3));
		    }
		    
	    }else{
		    \App::setLocale($locale);
	    	return redirect($locale.$request->getRequestUri());
	    }
*/

		return $next($request);
        
    }


}