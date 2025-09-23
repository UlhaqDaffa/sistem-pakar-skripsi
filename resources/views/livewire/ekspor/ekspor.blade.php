<?php

use Livewire\Volt\Component;

new class extends Component {
    public function exportToPdf(): void
    {
        session()->flash('message', 'Cape aku wak 😭');
        // Example: return response()->streamDownload(function () {
        // }, 'riwayat-konsultasi.pdf');
    }
};

?>

<div class="grid h-full flex-1 grid-cols-1 gap-4">

    <!-- Main Export Card -->
    <div class="bg-white/30 dark:bg-neutral-800/30 backdrop-blur-lg border border-white/40 dark:border-white/10 p-6 rounded-xl shadow-md flex flex-col items-center justify-center text-center">

        <!-- Header -->
        <div class="mb-6 flex-shrink-0">
            <svg class="h-16 w-16 text-primary dark:text-blue-400 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
            <h2 class="text-3xl font-semibold text-gray-900 dark:text-white">Ekspor Riwayat Konsultasi</h2>
        </div>

        <!-- Description -->
        <p class="text-gray-600 dark:text-neutral-300 mb-8 max-w-xl">
            Anda dapat mengekspor seluruh riwayat konsultasi Anda ke dalam format yang mudah dibaca.
            Pilih format di bawah ini untuk memulai proses ekspor.
        </p>

        <!-- Export Options -->
        <div class="flex flex-col sm:flex-row gap-4">
            <x-button-primary-lg wire:click="exportToPdf">
                Ekspor ke PDF
            </x-button-primary-lg>

            {{-- Add more export options here if needed, e.g., CSV --}}
            {{-- <x-button-secondary-lg>
                Ekspor ke CSV
            </x-button-secondary-lg> --}}
        </div>

        @if (session()->has('message'))
            <div class="mt-6 p-3 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

    </div>

</div>
