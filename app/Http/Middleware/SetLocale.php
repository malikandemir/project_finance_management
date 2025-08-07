<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Default to English
        $locale = 'en';
        
        // Get locale from session if available
        if (Session::has('locale')) {
            $sessionLocale = Session::get('locale');
            
            // Ensure locale is a string and supported
            if (is_string($sessionLocale) && in_array($sessionLocale, ['en', 'tr'])) {
                $locale = $sessionLocale;
            } else {
                // Reset invalid locale in session
                Session::put('locale', $locale);
            }
        }
        
        // Set the application locale
        App::setLocale($locale);
        
        // Process the request
        $response = $next($request);
        
        return $response;
    }
}
