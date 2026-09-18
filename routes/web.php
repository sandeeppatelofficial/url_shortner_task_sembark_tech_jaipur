<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ShortUrlController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// Public short-url resolver
Route::get('/s/{code}', [ShortUrlController::class, 'redirectToOriginal'])->name('short-urls.redirect');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/invite/{token}', [InvitationController::class, 'accept'])->name('invitations.accept');
    Route::post('/invite/{token}', [InvitationController::class, 'complete'])->name('invitations.complete');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/urls', [ShortUrlController::class, 'index'])->name('urls.index');

    Route::middleware('role:admin,member')->group(function () {
        Route::get('/urls/create', [ShortUrlController::class, 'create'])->name('urls.create');
        Route::post('/urls', [ShortUrlController::class, 'store'])->name('urls.store');
    });

    Route::middleware('role:superadmin,admin')->group(function () {
        Route::get('/invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
        Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');
    });
});
