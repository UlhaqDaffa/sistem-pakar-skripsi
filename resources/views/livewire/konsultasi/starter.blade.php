<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

    <div wire:ignore.self class="bg-white/30 dark:bg-neutral-800/30 backdrop-blur-lg border border-white/40 dark:border-white/10 p-8 rounded-xl shadow-md text-center flex flex-col items-center h-full">
        <div class="flex-grow flex flex-col items-center justify-center">
            <svg class="h-20 w-20 text-primary mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.898 20.562L16.5 21.75l-.398-1.188a3.375 3.375 0 00-2.9-2.9L12 17.25l1.188-.398a3.375 3.375 0 002.9-2.9L16.5 12.75l.398 1.188a3.375 3.375 0 002.9 2.9L21 17.25l-1.188.398a3.375 3.375 0 00-2.9 2.9z" />
            </svg>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Selamat Datang di Sesi Konsultasi</h1>
            <p class="mt-4 max-w-2xl text-gray-600 dark:text-neutral-300">
                Anda akan dipandu melalui beberapa pertanyaan untuk mengidentifikasi potensi masalah yang Anda hadapi. Proses ini sepenuhnya anonim dan hasilnya akan membantu Anda lebih memahami diri sendiri.
            </p>
            <livewire:konsultasi.starter-modal/>
        </div>
    </div>


