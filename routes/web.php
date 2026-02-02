<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('index');
})->name('home');

Route::get('/fitur', function () {
    return view('pages.feature');
})->name('feature');

Route::get('/privasi', function () {
    return view('pages.privacy');
})->name('privacy');

Route::get('/tentang', function () {
    return view('pages.about');
})->name('about');

Volt::route('dashboard', 'dashboard')
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
        Volt::route('konsultasi/hasil/{konsultasi?}', 'konsultasi.hasil')->name('konsultasi.hasil');
        Volt::route('riwayat', 'riwayat.riwayat')->name('riwayat.index');
        Volt::route('riwayat/{consultation}', 'riwayat.show')->name('riwayat.show');

        // Route PDF export harus didefinisikan sebelum route ekspor/{konsultasi?} untuk menghindari konflik
        Route::get('ekspor/pdf-all', [\App\Http\Controllers\EksporController::class, 'exportAllPdf'])->name('ekspor.pdf.all');
        Route::get('ekspor/pdf/{konsultasi}', [\App\Http\Controllers\EksporController::class, 'exportPdf'])->name('ekspor.pdf');

        Volt::route('ekspor/{konsultasi?}', 'ekspor.ekspor')->name('ekspor.index');
    });
});


require __DIR__.'/auth.php';
