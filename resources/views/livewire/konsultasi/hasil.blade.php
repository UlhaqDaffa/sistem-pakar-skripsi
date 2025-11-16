<?php

use Livewire\Volt\Component;
use App\Models\Konsultasi;

new class extends Component {
    public ?Konsultasi $konsultasi = null;
    public $areaRisetFinal = null;

    public function mount($konsultasi = null): void
    {
        if ($konsultasi instanceof Konsultasi) {
            $this->konsultasi = $konsultasi;
        } elseif (is_numeric($konsultasi)) {
            $this->konsultasi = Konsultasi::with(['areaRisetFinal', 'hasilMinat', 'hasilAkademik'])->find($konsultasi);
        }

        if ($this->konsultasi && $this->konsultasi->areaRisetFinal) {
            $this->areaRisetFinal = $this->konsultasi->areaRisetFinal;
        }
    }

    public function getTeknologiArray(): array
    {
        if (!$this->areaRisetFinal) {
            return [];
        }
        return array_map('trim', explode(',', $this->areaRisetFinal->kata_kunci_teknologi));
    }

    public function getMetodeArray(): array
    {
        if (!$this->areaRisetFinal) {
            return [];
        }
        return array_map('trim', explode(',', $this->areaRisetFinal->kata_kunci_metode));
    }

    public function getStudiKasusArray(): array
    {
        if (!$this->areaRisetFinal) {
            return [];
        }
        return array_map('trim', explode(',', $this->areaRisetFinal->contoh_studi_kasus));
    }

    public function getFormulasiJudul(): array
    {
        if (!$this->areaRisetFinal) {
            return [];
        }

        // Gunakan TitleFormulationService untuk generate judul
        $titleService = app(\App\Services\TitleFormulationService::class);
        $judul = $titleService->generateTitles($this->areaRisetFinal);

        // Jika tidak ada judul dari service, return array kosong
        return $judul ?: [];
    }
}; ?>

<div class="grid h-full flex-1 grid-cols-1 gap-4 lg:grid-cols-4">
    @if(!$konsultasi || !$areaRisetFinal)
        <!-- No Result -->
        <div class="lg:col-span-4 bg-white/30 dark:bg-neutral-800/30 backdrop-blur-lg border border-white/40 dark:border-white/10 p-6 md:p-8 rounded-xl shadow-md">
            <div class="text-center">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Hasil Konsultasi Tidak Ditemukan</h1>
                <p class="text-gray-600 dark:text-neutral-300 mb-6">
                    Konsultasi belum selesai atau hasil belum tersedia.
                </p>
                <x-button-primary-lg href="{{ route('dashboard') }}" wire:navigate>
                    Kembali ke Dashboard
                </x-button-primary-lg>
            </div>
        </div>
    @else
        <!-- Main Result Card - Peta Inspirasi Penelitian -->
        <div class="lg:col-span-3 bg-white/30 dark:bg-neutral-800/30 backdrop-blur-lg border border-white/40 dark:border-white/10 p-6 md:p-8 rounded-xl shadow-md flex flex-col">
            <!-- Header -->
            <div class="text-center flex-shrink-0 mb-8">
                <svg class="h-16 w-16 text-primary mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Peta Inspirasi Penelitian</h1>
                <p class="mt-2 max-w-2xl mx-auto text-gray-600 dark:text-neutral-300">
                    Berikut adalah rekomendasi area riset yang sesuai dengan minat dan kemampuan akademis Anda.
                </p>
            </div>

            <!-- Content Area -->
            <div class="flex-grow space-y-8">
                <!-- 1. Kesimpulan Utama -->
                <div class="bg-primary/10 dark:bg-primary/20 border-l-4 border-primary dark:border-blue-400 p-6 rounded-r-lg">
                    <h2 class="text-xl font-semibold text-primary dark:text-blue-300 mb-3">1. Kesimpulan Utama</h2>
                    <p class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ $areaRisetFinal->nama_area }}</p>
                    <p class="text-gray-800 dark:text-neutral-200 leading-relaxed">
                        {{ $areaRisetFinal->deskripsi }}
                    </p>
                </div>

                <!-- 2. Deskripsi -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">2. Deskripsi</h2>
                    <p class="text-gray-700 dark:text-neutral-300 leading-relaxed">
                        {{ $areaRisetFinal->deskripsi }}
                    </p>
                </div>

                <!-- 3. Teknologi & Metode Kunci -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">3. Teknologi & Metode Kunci</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Teknologi Utama -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 dark:text-neutral-200 mb-3">Teknologi Utama:</h3>
                            <ul class="space-y-2">
                                @foreach($this->getTeknologiArray() as $teknologi)
                                    @if(!empty(trim($teknologi)))
                                        <li class="flex items-start">
                                            <svg class="h-5 w-5 text-primary mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-gray-700 dark:text-neutral-300">{{ $teknologi }}</span>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>

                        <!-- Metode Kunci -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 dark:text-neutral-200 mb-3">Metode Kunci:</h3>
                            <ul class="space-y-2">
                                @foreach($this->getMetodeArray() as $metode)
                                    @if(!empty(trim($metode)))
                                        <li class="flex items-start">
                                            <svg class="h-5 w-5 text-primary mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-gray-700 dark:text-neutral-300">{{ $metode }}</span>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 4. Contoh Studi Kasus -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">4. Contoh Studi Kasus</h2>
                    <div class="space-y-3">
                        @foreach($this->getStudiKasusArray() as $index => $studiKasus)
                            @if(!empty(trim($studiKasus)))
                                <div class="bg-gray-50 dark:bg-neutral-800/50 p-4 rounded-lg border border-gray-200 dark:border-neutral-700">
                                    <div class="flex items-start">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center font-semibold mr-3">
                                            {{ $index + 1 }}
                                        </span>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">Studi Kasus {{ $index + 1 }}:</p>
                                            <p class="text-gray-700 dark:text-neutral-300 mt-1">{{ $studiKasus }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- 5. Formulasi Judul -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">5. Formulasi Judul</h2>
                    <p class="text-gray-600 dark:text-neutral-400 mb-4">
                        Berikut adalah beberapa pola formulasi judul yang dapat Anda gunakan sebagai inspirasi:
                    </p>
                    <div class="space-y-4">
                        @foreach($this->getFormulasiJudul() as $index => $judul)
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border-l-4 border-blue-500">
                                <p class="text-gray-800 dark:text-neutral-200 italic">
                                    "{{ $judul }}"
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex-shrink-0 flex flex-col sm:flex-row justify-center items-center gap-4 pt-8 mt-8 border-t border-gray-200/80 dark:border-neutral-700/80">
                <x-button-primary-lg href="{{ route('dashboard') }}" wire:navigate>
                    Kembali ke Dashboard
                </x-button-primary-lg>

                <x-button-secondary-lg>
                    Ekspor Hasil (PDF)
                </x-button-secondary-lg>

                <livewire:konsultasi.hasil-restart-modal/>
            </div>
        </div>

        <!-- Sidebar Card -->
        <div class="lg:col-span-1 bg-white/30 dark:bg-neutral-800/30 backdrop-blur-lg border border-white/40 dark:border-white/10 p-6 rounded-xl shadow-md flex flex-col">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex-shrink-0 mb-4">Informasi Konsultasi</h3>
            
            <div class="space-y-4 flex-grow">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Tanggal Konsultasi</p>
                    <p class="text-sm text-gray-900 dark:text-white mt-1">
                        {{ $konsultasi->created_at->format('d M Y, H:i') }}
                    </p>
                </div>

                @if($konsultasi->hasilMinat)
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Hasil Minat</p>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">
                            {{ $konsultasi->hasilMinat->nama_area }}
                        </p>
                    </div>
                @endif

                @if($konsultasi->hasilAkademik)
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Hasil Akademik</p>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">
                            {{ $konsultasi->hasilAkademik->nama_area }}
                        </p>
                    </div>
                @endif

                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Rekomendasi Final</p>
                    <p class="text-sm font-semibold text-primary dark:text-blue-400 mt-1">
                        {{ $areaRisetFinal->nama_area }}
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
