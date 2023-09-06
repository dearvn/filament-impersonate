<?php

use Illuminate\Support\Facades\Route;
use Octopy\Impersonate\ImpersonateManager;
use Octopy\Impersonate\Http\Middleware\ImpersonateMiddleware;

Route::get('filament-impersonate/leave', function() {
    if(!app(ImpersonateManager::class)->isInImpersonation()) {
        return redirect('/');
    }

    app(ImpersonateManager::class)->leave();

    return redirect(session()->pull('impersonate.back_to'));
})->name('filament-impersonate.leave')->middleware([ImpersonateMiddleware::class,'web']);
