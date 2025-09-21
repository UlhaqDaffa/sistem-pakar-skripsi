<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <!-- Welcome Card & Main CTA -->
        <div class="bg-white dark:bg-neutral-800 p-6 rounded-xl shadow-md border border-neutral-200 dark:border-neutral-700">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Selamat Datang, {{ auth()->user()->name ?? 'Pengguna' }}!</h2>
            <p class="text-gray-600 dark:text-neutral-300 mb-6">Siap untuk memulai konsultasi baru atau melihat riwayat Anda?</p>
            <a href="{{ route('konsultasi') }}"
               class="relative inline-flex items-center px-6 py-3 border border-indigo-600 text-base font-medium rounded-md text-indigo-600 overflow-hidden group
                      focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                      x-data="{ hovered: false }"
                      @mouseenter="hovered = true"
                      @mouseleave="hovered = false">
                <span class="absolute inset-0 bg-indigo-600 transition-all duration-300 ease-out"
                      :class="{ 'w-full': hovered, 'w-0': !hovered }">
                </span>
                <span class="relative flex items-center z-10 transition-colors duration-300 ease-out"
                      :class="{ 'text-white': hovered, 'text-indigo-600': !hovered }">
                    Mulai Konsultasi Baru
                    <svg class="ml-3 -mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </span>
            </a>
        </div>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">

            <!-- Total Rekomendasi -->
            <div class="bg-white dark:bg-neutral-800 p-6 rounded-xl shadow-md border border-neutral-200 dark:border-neutral-700">
                <h3 class="text-lg font-medium text-gray-500 dark:text-neutral-400">Total Konsultasi</h3>
                <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">{{ $totalConsultations ?? '0' }}</p>
            </div>

            <!-- Hasil Rekomendasi Terakhir -->
            <div class="bg-white dark:bg-neutral-800 p-6 rounded-xl shadow-md border border-neutral-200 dark:border-neutral-700">
                <h3 class="text-lg font-medium text-gray-500 dark:text-neutral-400">Hasil Konsultasi Terakhir</h3>
                <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $lastConsultationResult ?? 'Belum ada' }}</p>
            </div>

            <!-- Kartu ekspor -->
            <div class="bg-white dark:bg-neutral-800 p-0 rounded-xl shadow-md border dark:border-neutral-700 flex items-stretch justify-stretch">
                <a href="{{ route('ekspor') }}"
                   class="relative flex-1 flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-secondary overflow-hidden group
                          focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                          x-data="{ hovered: false }"
                          @mouseenter="hovered = true"
                          @mouseleave="hovered = false">
                    <span class="absolute inset-0 bg-secondary transition-all duration-500 ease-out"
                          :class="{ 'w-full': hovered, 'w-0': !hovered }">
                        </span>
                    <span class="relative flex items-center z-10 transition-colors duration-400 ease-out"
                          :class="{ 'text-white': hovered, 'text-secondary': !hovered }">
                        Ekspor Hasil
                        <svg class="ml-3 -mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </a>
            </div>

        </div>

        <div class="grid gap-4 md:grid-cols-1 flex-1">

            <!-- Riwayat rekomendasi -->
            <div class="bg-white dark:bg-neutral-800 p-6 rounded-xl shadow-md border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Riwayat Konsultasi Terakhir</h3>
                @if(isset($recentConsultations) && $recentConsultations->count() > 0)
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
                    <div class="mt-4 text-right">
                        <a href="{{ route('riwayat') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">Lihat Semua &rarr;</a>
                    </div>
                @else
                    <p class="text-gray-500 dark:text-neutral-400">Belum ada riwayat konsultasi.</p>
                @endif
            </div>

        </div>

    </div>
</x-layouts.app>
