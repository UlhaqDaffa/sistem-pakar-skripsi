<?php

use Livewire\Volt\Component;
use App\Models\Konsultasi;

new class extends Component {
    public ?Konsultasi $konsultasi = null;
    public $areaRisetFinal = null;
    public string $hasilTab = 'minat';

    public function mount($konsultasi = null): void
    {
        if ($konsultasi instanceof Konsultasi) {
            $this->konsultasi = $konsultasi->loadMissing([
                'areaRisetFinal.tags',
                'hasilMinat.tags',
                'hasilAkademik.tags',
            ]);
        } elseif (is_numeric($konsultasi)) {
            $this->konsultasi = Konsultasi::with([
                'areaRisetFinal.tags',
                'hasilMinat.tags',
                'hasilAkademik.tags',
            ])->find($konsultasi);
        }

        if ($this->konsultasi && $this->konsultasi->areaRisetFinal) {
            $this->areaRisetFinal = $this->konsultasi->areaRisetFinal;
        }

        if ($this->konsultasi && !$this->konsultasi->hasilMinat && $this->konsultasi->hasilAkademik) {
            $this->hasilTab = 'akademik';
        }
    }

    public function selectHasilTab(string $tab): void
    {
        if (in_array($tab, ['minat', 'akademik'], true)) {
            $this->hasilTab = $tab;
        }
    }

    public function getAreaUntukTab(): ?\App\Models\AreaRiset
    {
        if (!$this->konsultasi) {
            return null;
        }

        return $this->hasilTab === 'akademik'
            ? $this->konsultasi->hasilAkademik
            : $this->konsultasi->hasilMinat;
    }

    public function getTeknologiArray(?\App\Models\AreaRiset $area = null): array
    {
        $area ??= $this->areaRisetFinal;
        if (!$area) {
            return [];
        }

        return $area->tags
            ->where('tipe', 'TEKNOLOGI')
            ->pluck('nama_tag')
            ->filter()
            ->values()
            ->all();
    }

    public function getMetodeArray(?\App\Models\AreaRiset $area = null): array
    {
        $area ??= $this->areaRisetFinal;
        if (!$area) {
            return [];
        }

        return $area->tags
            ->where('tipe', 'METODE')
            ->pluck('nama_tag')
            ->filter()
            ->values()
            ->all();
    }

    public function getStudiKasusArray(?\App\Models\AreaRiset $area = null): array
    {
        $area ??= $this->areaRisetFinal;
        if (!$area || !$area->contoh_studi_kasus) {
            return [];
        }
        return array_map('trim', explode(',', $area->contoh_studi_kasus));
    }

    public function getFormulasiJudul(?\App\Models\AreaRiset $area = null): array
    {
        $area ??= $this->areaRisetFinal;
        if (!$area) {
            return [];
        }

        // Gunakan TitleFormulationService untuk generate judul
        $titleService = app(\App\Services\TitleFormulationService::class);
        $judul = $titleService->generateTitles($area);

        // Jika tidak ada judul dari service, return array kosong
        return $judul ?: [];
    }

    public function getLevelLabel(?\App\Models\AreaRiset $area = null): string
    {
        $area ??= $this->areaRisetFinal;
        if (!$area) {
            return '-';
        }

        $map = [
            1 => 'Level 1 • Konseptual / Rendah',
            2 => 'Level 2 • Pengembangan / Menengah',
            3 => 'Level 3 • Deep Tech / Tinggi',
        ];

        return $map[$area->level_kesulitan] ?? 'Level tidak diketahui';
    }

    public function getTargetArketipeLabel(?\App\Models\AreaRiset $area = null): string
    {
        $area ??= $this->areaRisetFinal;
        if (!$area) {
            return '-';
        }

        return match ($area->target_arketipe) {
            'CREATOR' => 'Creator • Eksperimen & Implementasi UI/Produk',
            'ANALIS' => 'Analis • Data, insight, & validasi',
            'ARCHITECT' => 'Architect • Sistem besar & integrasi',
            default => 'General • Fleksibel lintas arketipe',
        };
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
                    <div class="flex flex-wrap items-center gap-3 mt-4 text-sm">
                        <span class="px-3 py-1 rounded-full bg-white/60 dark:bg-neutral-900/30 text-gray-800 dark:text-neutral-100 border border-primary/30">
                            {{ $this->getTargetArketipeLabel() }}
                        </span>
                        <span class="px-3 py-1 rounded-full bg-white/60 dark:bg-neutral-900/30 text-gray-800 dark:text-neutral-100 border border-emerald-300/50">
                            {{ $this->getLevelLabel() }}
                        </span>
                        @if($areaRisetFinal->tipe_sistem)
                            <span class="px-3 py-1 rounded-full bg-white/60 dark:bg-neutral-900/30 text-gray-800 dark:text-neutral-100 border border-indigo-300/50">
                                {{ $areaRisetFinal->tipe_sistem }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Tabs hasil minat vs akademik -->
                @php($activeArea = $this->getAreaUntukTab())
                <div class="pt-4">
                    <div class="flex flex-wrap gap-3 border-b border-gray-200 dark:border-neutral-700 pb-4">
                        <button
                            wire:click="selectHasilTab('minat')"
                            class="px-4 py-2 rounded-full text-sm font-semibold transition-all duration-200 {{ $this->hasilTab === 'minat' ? 'bg-primary text-white shadow' : 'bg-gray-100 dark:bg-neutral-800 text-gray-600 dark:text-neutral-300' }}"
                        >
                            Hasil Minat (Rule-Based)
                        </button>
                        <button
                            wire:click="selectHasilTab('akademik')"
                            class="px-4 py-2 rounded-full text-sm font-semibold transition-all duration-200 {{ $this->hasilTab === 'akademik' ? 'bg-primary text-white shadow' : 'bg-gray-100 dark:bg-neutral-800 text-gray-600 dark:text-neutral-300' }}"
                        >
                            Hasil Akademik (Decision Tree)
                        </button>
                    </div>

                    <div class="mt-6 space-y-8">
                        @if($activeArea)
                            <div class="bg-white/70 dark:bg-neutral-900/40 p-6 rounded-xl border border-gray-200/70 dark:border-neutral-700/70">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                    {{ $activeArea->nama_area }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">
                                    {{ $this->hasilTab === 'minat' ? 'Rekomendasi berdasarkan preferensi/minat Anda.' : 'Rekomendasi berdasarkan performa akademik pada mata kuliah kunci.' }}
                                </p>
                                <p class="text-gray-700 dark:text-neutral-300 leading-relaxed">
                                    {{ $activeArea->deskripsi }}
                                </p>
                                <div class="flex flex-wrap items-center gap-3 mt-4 text-xs">
                                    <span class="px-3 py-1 rounded-full bg-white/80 dark:bg-neutral-800/60 text-gray-700 dark:text-neutral-200 border border-primary/20">
                                        {{ $this->getTargetArketipeLabel($activeArea) }}
                                    </span>
                                    <span class="px-3 py-1 rounded-full bg-white/80 dark:bg-neutral-800/60 text-gray-700 dark:text-neutral-200 border border-emerald-300/50">
                                        {{ $this->getLevelLabel($activeArea) }}
                                    </span>
                                    @if($activeArea->tipe_sistem)
                                        <span class="px-3 py-1 rounded-full bg-white/80 dark:bg-neutral-800/60 text-gray-700 dark:text-neutral-200 border border-indigo-300/50">
                                            {{ $activeArea->tipe_sistem }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Teknologi & Metode Kunci</h2>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-800 dark:text-neutral-200 mb-3">Teknologi Utama:</h3>
                                        @php($teknologiTags = $this->getTeknologiArray($activeArea))
                                        @if(empty($teknologiTags))
                                            <p class="text-sm text-gray-500 dark:text-neutral-400">Belum ada tag teknologi yang ditautkan.</p>
                                        @else
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($teknologiTags as $teknologi)
                                                    <span class="px-3 py-1 text-sm rounded-full bg-primary/10 text-primary dark:bg-primary/20 dark:text-blue-200">
                                                        {{ $teknologi }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <div>
                                        <h3 class="text-lg font-medium text-gray-800 dark:text-neutral-200 mb-3">Metode Kunci:</h3>
                                        @php($metodeTags = $this->getMetodeArray($activeArea))
                                        @if(empty($metodeTags))
                                            <p class="text-sm text-gray-500 dark:text-neutral-400">Belum ada tag metode yang ditautkan.</p>
                                        @else
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($metodeTags as $metode)
                                                    <span class="px-3 py-1 text-sm rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200">
                                                        {{ $metode }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Contoh Studi Kasus</h2>
                                <div class="space-y-3">
                                    @forelse($this->getStudiKasusArray($activeArea) as $index => $studiKasus)
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
                                    @empty
                                        <p class="text-sm text-gray-500 dark:text-neutral-400">Belum ada contoh studi kasus.</p>
                                    @endforelse
                                </div>
                            </div>

                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Formulasi Judul</h2>
                                <p class="text-gray-600 dark:text-neutral-400 mb-4">
                                    Berikut adalah beberapa pola formulasi judul yang dapat Anda gunakan sebagai inspirasi:
                                </p>
                                <div class="space-y-4">
                                    @forelse($this->getFormulasiJudul($activeArea) as $judul)
                                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border-l-4 border-blue-500">
                                            <p class="text-gray-800 dark:text-neutral-200 italic">
                                                "{{ $judul }}"
                                            </p>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500 dark:text-neutral-400">Belum ada pola judul untuk area ini.</p>
                                    @endforelse
                                </div>
                            </div>
                        @else
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-6 text-yellow-800 dark:text-yellow-100">
                                <p class="font-semibold mb-1">Belum ada rekomendasi pada tab ini.</p>
                                <p class="text-sm">Lengkapi data konsultasi atau lakukan input nilai mata kuliah untuk melihat rekomendasi.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex-shrink-0 flex flex-col sm:flex-row justify-center items-center gap-4 pt-8 mt-8 border-t border-gray-200/80 dark:border-neutral-700/80">
                <x-button-primary-lg href="{{ route('dashboard') }}" wire:navigate>
                    Kembali ke Dashboard
                </x-button-primary-lg>

                <a
                    href="{{ route('ekspor.pdf', ['konsultasi' => $konsultasi->id]) }}"
                    class="inline-flex items-center justify-center px-6 py-3 rounded-xl border border-primary/30 text-primary hover:bg-primary/10 font-semibold transition-colors duration-200"
                >
                    Ekspor Hasil (PDF)
                </a>

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
