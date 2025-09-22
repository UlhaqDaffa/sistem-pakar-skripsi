<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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


    Route::prefix('konsultasi')->name('konsultasi.')->middleware(['verified'])->group(function () {
        Volt::route('/', 'konsultasi.starter')->name('starter');
        Volt::route('/proses/{step?}', 'konsultasi.proses')->name('proses');
        Volt::route('/hasil', 'konsultasi.hasil')->name('hasil');
    });
});

require __DIR__.'/auth.php';
