<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    Volt::route('login', 'admin.auth.login')->name('login');
});

Route::middleware(['auth', 'admin'])->group(function() {
    // Dashboard
    Volt::route('dashboard', 'admin.dashboard')->name('dashboard');

    // Master Data
    Volt::route('users', 'admin.pages.users.index')->name('users.index');
    Volt::route('minat', 'admin.pages.minat.index')->name('minat.index');
    Volt::route('area-riset', 'admin.pages.researchArea.index')->name('area-riset.index');
    Volt::route('tags', 'admin.pages.tags.index')->name('tags.index');

    // Knowledge Base
    Volt::route('pertanyaan', 'admin.pages.questions.index')->name('pertanyaan.index');
    Volt::route('rules', 'admin.pages.rules.index')->name('rules.index');
    Volt::route('pola-judul', 'admin.pages.titlePattern.index')->name('pola-judul.index');

    // System
    Volt::route('riwayat-konsultasi', 'admin.pages.histori.index')->name('riwayat-konsultasi.index');
    Volt::route('riwayat-konsultasi/{consultation}', 'admin.pages.histori.show')->name('riwayat-konsultasi.show');
    Volt::route('training-model', 'admin.pages.training-model.index')->name('training-model.index');

    // Settings
    Route::redirect('settings', 'settings/profil');
    Volt::route('settings/profil', 'admin.settings.profil')->name('settings.profil');
    Volt::route('settings/password', 'admin.settings.password')->name('settings.password');
    Volt::route('settings/tampilan', 'admin.settings.tampilan')->name('settings.tampilan');
    Volt::route('settings/pembobotan', 'admin.settings.pembobotan')->name('settings.pembobotan');

    // Legacy routes (untuk backward compatibility)
    Volt::route('aturan', 'admin.rules.aturan')->name('aturan');
    Volt::route('topik', 'admin.topics.topik')->name('topik');
});

