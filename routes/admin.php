<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    Volt::route('login', 'admin.auth.login')->name('login');
});

Route::middleware(['auth', 'admin'])->group(function() {
    Volt::route('dashboard', 'admin.dashboard')->name('dashboard');

    Route::redirect('settings/admin', 'settings/admin/profil');

    Volt::route('settings/profil', 'admin.settings.profil')->name('settings.profil');
    Volt::route('settings/password', 'admin.settings.password')->name('settings.password');
    Volt::route('settings/tampilan', 'admin.settings.tampilan')->name('settings.tampilan');
});
