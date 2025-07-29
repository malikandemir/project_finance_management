<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class LanguageController extends Controller
{
    /**
     * Switch the application language.
     *
     * @param  Request  $request
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLang(Request $request, $locale)
    {
        // Check if the locale exists in our allowed locales
        if (in_array($locale, ['en', 'tr'])) {
            // Store the locale in session
            Session::put('locale', $locale);
            App::setLocale($locale);
        }
        
        // Check if a redirect parameter was provided
        if ($request->has('redirect')) {
            $redirectUrl = $request->input('redirect');
            
            // Less strict URL validation - just make sure it's not empty
            if (!empty($redirectUrl)) {
                // If it's a relative URL, make it absolute
                if (strpos($redirectUrl, 'http') !== 0) {
                    if ($redirectUrl[0] !== '/') {
                        $redirectUrl = '/' . $redirectUrl;
                    }
                }
                
                return redirect()->to($redirectUrl);
            }
        }
        
        // Get the previous URL from the request as fallback
        $previousUrl = $request->header('referer');
        
        // Less strict check for previous URL
        if (!empty($previousUrl)) {
            return redirect()->to($previousUrl);
        }
        
        // If no valid redirect URL, redirect to home
        return redirect('/');
    }
}
