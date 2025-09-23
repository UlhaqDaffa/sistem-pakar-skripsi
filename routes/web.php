<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profil');

    Volt::route('settings/profil', 'settings.profil')->name('settings.profil');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/tampilan', 'settings.tampilan')->name('settings.tampilan');

    Route::middleware(['verified'])->group(function () {
        Volt::route('konsultasi/starter', 'konsultasi.starter')->name('konsultasi.starter');
        Volt::route('konsultasi/proses/{step?}', 'konsultasi.proses')->name('konsultasi.proses');
        Volt::route('konsultasi/hasil', 'konsultasi.hasil')->name('konsultasi.hasil');
        Volt::route('riwayat', 'riwayat.riwayat')->name('riwayat.index');
        Volt::route('riwayat/{consultation}', 'riwayat.show')->name('riwayat.show');
        Volt::route('ekspor', 'ekspor.ekspor')->name('ekspor.index');
    });
});


require __DIR__.'/auth.php';
