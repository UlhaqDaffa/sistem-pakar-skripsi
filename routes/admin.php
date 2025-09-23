<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Admin Authentication Routes
Route::middleware('guest')->group(function () {
    Volt::route('login', 'admin.auth.login')->name('login');
});

// Authenticated Admin Routes
Route::middleware(['auth', 'admin'])->group(function() {
    // Admin dashboard and other admin routes can go here.
    // For example:
    Volt::route('dashboard', 'admin.dashboard')->name('dashboard');
});
