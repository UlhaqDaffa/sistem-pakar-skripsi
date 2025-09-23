<?php

use function Livewire\Volt\{layout};

//

?>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <!-- Welcome Card & Main CTA -->
        <div class="p-6 rounded-xl shadow-lg bg-gradient-to-l from-blue-800/50 to-blue-900/80 backdrop-blur-lg border border-white/20">
            <h2 class="text-2xl font-semibold text-white mb-4">Selamat Datang, {{ auth()->user()->name ?? 'Pengguna' }}!</h2>
            <p class="text-blue-200 mb-6">Siap untuk memulai konsultasi baru atau melihat riwayat Anda?</p>
            <a href="{{ route('konsultasi.starter') }}"
               class="relative inline-flex items-center px-6 py-3 border border-white text-base font-medium rounded-md text-white overflow-hidden group
                      focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white"
                      x-data="{ hovered: false }"
                      @mouseenter="hovered = true"
                      @mouseleave="hovered = false"
                      wire:click="redirect(route('konsultasi.starter'), navigate: true)">
                <span class="absolute inset-0 bg-white transition-all duration-300 ease-out"
                      :class="{ 'w-full': hovered, 'w-0': !hovered }">
                </span>
                <span class="relative flex items-center z-10 transition-colors duration-300 ease-out"
                      :class="{ 'text-blue-900': hovered, 'text-white': !hovered }">
                    Mulai Konsultasi Baru
                    <svg class="ml-3 -mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </span>
            </a>
        </div>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">

            <!-- Total Konsultasi -->
            <div class="bg-white/30 dark:bg-neutral-800/30 backdrop-blur-lg border border-white/40 dark:border-white/10 p-6 rounded-xl shadow-md flex items-center">
                <div class="flex-shrink-0 mr-4">
                    <svg class="h-12 w-12 text-gray-500 dark:text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-gray-500 dark:text-neutral-400">Total Konsultasi</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">{{ $totalConsultations ?? '0' }}</p>
                </div>
            </div>

            <!-- Hasil Konsultasi Terakhir -->
            <div class="bg-white/30 dark:bg-neutral-800/30 backdrop-blur-lg border border-white/40 dark:border-white/10 p-6 rounded-xl shadow-md flex items-center">
                <div class="flex-shrink-0 mr-4">
                    <svg class="h-12 w-12 text-gray-500 dark:text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-gray-500 dark:text-neutral-400">Hasil Konsultasi Terakhir</h3>
                    <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $lastConsultationResult ?? 'Belum ada' }}</p>
                </div>
            </div>

            <!-- Kartu ekspor -->
            <a href="{{ route('ekspor.index') }}"
               class="relative bg-white/30 dark:bg-neutral-800/30 backdrop-blur-lg border border-white/40 dark:border-white/10 p-6 rounded-xl shadow-md flex items-center overflow-hidden focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
               x-data="{ hovered: false }"
               @mouseenter="hovered = true"
               @mouseleave="hovered = false">

                <span class="absolute inset-0 bg-secondary transition-all duration-600 ease-out"
                      :class="{ 'w-full': hovered, 'w-0': !hovered }">
                </span>

                <div class="relative z-10 flex items-center w-full">
                    <div class="flex-shrink-0 mr-4">
                        <svg :class="hovered ? 'text-white' : 'text-gray-500 dark:text-neutral-400'"
                             class="h-12 w-12 transition-colors duration-300"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 :class="hovered ? 'text-white' : 'text-gray-900 dark:text-white'"
                            class="text-lg font-medium transition-colors duration-300">
                            Ekspor Riwayat
                        </h3>
                        <p :class="hovered ? 'text-gray-200' : 'text-gray-500 dark:text-neutral-400'"
                           class="transition-colors duration-300">
                            Unduh data konsultasi
                        </p>
                    </div>
                    <div class="ml-4">
                         <svg :class="hovered ? 'text-white translate-x-1' : 'text-gray-400 dark:text-neutral-500'"
                              class="h-6 w-6 transition-all duration-300"
                              xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </a>

        </div>

        <div class="grid gap-4 md:grid-cols-1 flex-1">

            <!-- Riwayat rekomendasi -->
            <div class="bg-white/30 dark:bg-neutral-800/30 backdrop-blur-lg border border-white/40 dark:border-white/10 p-6 rounded-xl shadow-md overflow-hidden flex flex-col">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex-shrink-0">Riwayat Konsultasi Terakhir</h3>
                @if(isset($recentConsultations) && $recentConsultations->count() > 0)
                    <div class="flex-grow overflow-y-auto">
                        <ul role="list" class="divide-y divide-gray-200 dark:divide-neutral-700">
                            @foreach($recentConsultations as $consultation)
                                <li class="py-4 flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $consultation->result ?? 'N/A' }}</p>
                                        <p class="text-sm text-gray-500 dark:text-neutral-400">{{ $consultation->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                    <a href="{{ route('riwayat.show', $consultation->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium">Detail</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="mt-4 text-right flex-shrink-0">
                        <a href="{{ route('riwayat.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">Lihat Semua &rarr;</a>
                    </div>
                @else
                    <div class="flex-grow flex flex-col items-center justify-center text-center">
                        <svg class="h-16 w-16 text-gray-400 dark:text-neutral-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                        <p class="text-gray-500 dark:text-neutral-400">Belum ada riwayat konsultasi.</p>
                    </div>
                @endif
            </div>

        </div>

    </div>

