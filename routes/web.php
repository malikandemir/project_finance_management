<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;

// Root URL is now handled by Filament admin panel

// Language test route
Route::get('/language-test', function () {
    return view('language-test');
})->name('language.test');

Route::get('/lang/{locale}', [LanguageController::class, 'switchLang'])->name('lang.switch');
