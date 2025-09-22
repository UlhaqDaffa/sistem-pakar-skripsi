<x-layouts.app :title="__('Konsultasi')" :stashed="true">
    <div x-data="{ started: false }" class="flex h-full flex-1 flex-col">

        <!-- Starter Screen -->
        <div x-show="!started" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="flex-1">
            <div class="bg-white/30 dark:bg-neutral-800/30 backdrop-blur-lg border border-white/40 dark:border-white/10 p-8 rounded-xl shadow-md text-center flex flex-col items-center h-full">
                <div class="flex-grow flex flex-col items-center justify-center">
                    <svg class="h-20 w-20 text-primary mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.898 20.562L16.5 21.75l-.398-1.188a3.375 3.375 0 00-2.9-2.9L12 17.25l1.188-.398a3.375 3.375 0 002.9-2.9L16.5 12.75l.398 1.188a3.375 3.375 0 002.9 2.9L21 17.25l-1.188.398a3.375 3.375 0 00-2.9 2.9z" />
                    </svg>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Selamat Datang di Sesi Konsultasi</h1>
                    <p class="mt-4 max-w-2xl text-gray-600 dark:text-neutral-300">
                        Anda akan dipandu melalui beberapa pertanyaan untuk mengidentifikasi potensi masalah yang Anda hadapi. Proses ini sepenuhnya anonim dan hasilnya akan membantu Anda lebih memahami diri sendiri.
                    </p>
                    <button @click="started = true"
                        class="relative mt-8 px-8 py-3 text-lg font-semibold rounded-lg bg-primary text-white overflow-hidden group focus:outline-none transition-all duration-200 focus:ring-2 focus:ring-offset-2 focus:ring-primary/70 dark:focus:ring-offset-neutral-800"
                        x-data="{ hovered: false }"
                        @mouseenter="hovered = true"
                        @mouseleave="hovered = false">
                        <span class="absolute inset-0 bg-white transition-all duration-300 ease-out"
                              :class="{ 'w-full': hovered, 'w-0': !hovered }">
                        </span>
                        <span class="relative z-10 transition-colors duration-300 ease-out"
                              :class="{ 'text-primary': hovered, 'text-white': !hovered }">
                            Mulai Konsultasi
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Consultation View -->
        <div x-show="started" class="grid h-full flex-1 grid-cols-1 gap-4 lg:grid-cols-4" x-transition:enter="ease-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

            <!-- Main Consultation Card -->
            <div class="lg:col-span-3 bg-white/30 dark:bg-neutral-800/30 backdrop-blur-lg border border-white/40 dark:border-white/10 p-6 rounded-xl shadow-md flex flex-col">

                <!-- Header & Progress -->
                <div class="flex-shrink-0 mb-6">
                    <div class="flex items-center gap-4">
                        <button @click="started = false" title="Kembali" class="flex-shrink-0 p-2 rounded-full text-gray-500 dark:text-neutral-400 hover:bg-gray-500/10 dark:hover:bg-neutral-700/50 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary/50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Mulai Konsultasi</h2>
                            <p class="text-gray-600 dark:text-neutral-300 mt-1">Jawab pertanyaan berikut untuk mendapatkan hasil.</p>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-4">
                        <div class="flex justify-between mb-1">
                            <span class="text-base font-medium text-primary dark:text-blue-400">Proses Konsultasi</span>
                            <span class="text-sm font-medium text-primary dark:text-blue-400">Langkah 1 dari 5</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="bg-primary h-2.5 rounded-full" style="width: 20%"></div>
                        </div>
                    </div>
                </div>

                <!-- Question Area -->
                <div class="flex-grow flex flex-col items-center justify-center text-center px-4">
                    <h3 class="text-xl md:text-3xl font-medium text-gray-900 dark:text-white">Apakah Anda sering merasa kesulitan untuk fokus saat belajar?</h3>
                    <p class="text-gray-500 dark:text-neutral-400 mt-2 max-w-2xl">Ini bisa menjadi indikasi dari beberapa faktor, seperti kelelahan, stres, atau gangguan lainnya.</p>

                    <!-- Answer Options -->
                    <div class="mt-8 flex gap-4">
                        <button class="px-8 py-3 text-lg font-semibold rounded-lg bg-primary text-white hover:bg-primary/90 focus:outline-none transition-all duration-200 hover:ring-2 hover:ring-primary/70 dark:hover:ring-offset-neutral-800">
                            Ya
                        </button>
                        <button class="px-8 py-3 text-lg font-semibold rounded-lg dark:bg-neutral-700 text-gray-800 dark:text-white hover:bg-gray-300 dark:hover:bg-neutral-600 focus:outline-none transition-all duration-200 hover:ring-2 hover:ring-gray-400 dark:hover:ring-neutral-500 dark:hover:ring-offset-neutral-800">
                            Tidak
                        </button>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="flex-shrink-0 flex justify-end mt-6">
                    <button class="px-6 py-2 font-medium rounded-md text-white bg-primary hover:bg-primary/90 transition-all duration-200 hover:ring-2 hover:ring-primary/70 dark:hover:ring-offset-neutral-800">
                        Berikutnya
                    </button>
                </div>
            </div>

            <!-- Summary Card -->
            <div class="lg:col-span-1 bg-white/30 dark:bg-neutral-800/30 backdrop-blur-lg border border-white/40 dark:border-white/10 p-6 rounded-xl shadow-md flex flex-col">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex-shrink-0">Ringkasan Jawaban</h3>
                <div class="mt-4 flex-grow overflow-y-auto">
                    <ul class="space-y-3">
                        <li class="flex justify-between items-center text-sm text-gray-400 dark:text-neutral-500">
                            <span>Belum ada jawaban yang diberikan.</span>
                        </li>
                    </ul>
                </div>
                <div class="mt-4 flex-shrink-0">
                    <button class="w-full px-6 py-2 font-medium rounded-md text-red-600 bg-red-500/10 hover:bg-red-500/20 transition-all duration-200 hover:ring-2 hover:ring-red-500/30">
                        Ulangi Konsultasi
                    </button>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>

