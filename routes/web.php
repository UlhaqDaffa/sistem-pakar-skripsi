<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('konsultasi', 'konsultasi')
    ->middleware(['auth', 'verified'])
    ->name('konsultasi');

Route::view('riwayat', 'riwayat')
    ->middleware(['auth', 'verified'])
    ->name('riwayat');

Route::view('ekspor', 'ekspor')
    ->middleware(['auth', 'verified'])
    ->name('ekspor');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profil');

    Volt::route('settings/profil', 'settings.profil')->name('settings.profil');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/tampilan', 'settings.tampilan')->name('settings.tampilan');
});

require __DIR__.'/auth.php';
