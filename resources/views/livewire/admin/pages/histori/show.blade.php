<?php

use Livewire\Volt\Component;
use App\Models\Konsultasi;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app-admin')] class extends Component {
    public ?Konsultasi $consultation = null;

    public function mount($consultation = null): void
    {
        if ($consultation instanceof Konsultasi) {
            $this->consultation = $consultation;
        } elseif (is_numeric($consultation)) {
            $this->consultation = Konsultasi::with(['user', 'areaRisetFinal', 'hasilMinat', 'hasilAkademik', 'jawabanKonsultasis.opsiJawaban.pertanyaan.kategori', 'nilaiMataKuliah.mataKuliahKunci'])
                ->findOrFail($consultation);
        }
    }

    public function backToHistory(): void
    {
        $this->redirect(route('admin.riwayat-konsultasi.index'), navigate: true);
    }
}; ?>

<div>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        @if(!$consultation)
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-lg border border-gray-200 dark:border-neutral-700 p-8">
                <div class="text-center">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Konsultasi Tidak Ditemukan</h2>
                    <p class="text-gray-600 dark:text-neutral-300 mb-6">Konsultasi yang Anda cari tidak ditemukan.</p>
                    <button wire:click="backToHistory"
                            class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                        Kembali ke Histori
                    </button>
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-lg border border-gray-200 dark:border-neutral-700 p-6 md:p-8">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <button wire:click="backToHistory"
                                class="p-2 rounded-lg text-gray-500 dark:text-neutral-400 hover:bg-gray-100 dark:hover:bg-neutral-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Konsultasi</h1>
                            <p class="text-sm text-gray-500 dark:text-neutral-400">{{ $consultation->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- User Info (Admin view) -->
                <div class="mb-6 p-4 bg-gray-50 dark:bg-neutral-700/50 rounded-lg">
                    <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Pengguna</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $consultation->user?->name ?? '-' }}</p>
                    <p class="text-sm text-gray-600 dark:text-neutral-300">{{ $consultation->user?->email ?? '-' }}</p>
                </div>

                <div class="space-y-6">
                    <!-- Status -->
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Status</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mt-1 {{ $consultation->status === 'selesai' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' }}">
                            {{ ucfirst($consultation->status) }}
                        </span>
                    </div>

                    <!-- Rekomendasi Final -->
                    @if($consultation->areaRisetFinal)
                        <div class="bg-primary/10 dark:bg-primary/20 border-l-4 border-primary dark:border-blue-400 p-4 rounded-r-lg">
                            <p class="text-sm font-medium text-primary dark:text-blue-300 mb-2">Rekomendasi Final</p>
                            <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ $consultation->areaRisetFinal->nama_area }}</p>
                            <p class="text-gray-700 dark:text-neutral-300 mt-2">{{ $consultation->areaRisetFinal->deskripsi }}</p>
                        </div>
                    @endif

                    <!-- Hasil Minat & Akademik -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($consultation->hasilMinat)
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <p class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-2">Hasil Minat</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $consultation->hasilMinat->nama_area }}</p>
                            </div>
                        @endif
                        @if($consultation->hasilAkademik)
                            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                                <p class="text-sm font-medium text-green-800 dark:text-green-300 mb-2">Hasil Akademik</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $consultation->hasilAkademik->nama_area }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Nilai Mata Kuliah -->
                    @if($consultation->nilaiMataKuliah->count() > 0)
                        <div>
                            <p class="text-lg font-medium text-gray-700 dark:text-neutral-300 mb-3">Nilai Mata Kuliah</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($consultation->nilaiMataKuliah as $nilai)
                                    <div class="bg-gray-50 dark:bg-neutral-700/50 p-3 rounded-lg">
                                        <p class="text-xs font-medium text-gray-500 dark:text-neutral-400">{{ $nilai->mataKuliahKunci->nama_mata_kuliah ?? '-' }}</p>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($nilai->nilai, 1) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Jawaban Konsultasi -->
                    @if($consultation->jawabanKonsultasis->count() > 0)
                        <div>
                            <p class="text-lg font-medium text-gray-700 dark:text-neutral-300 mb-3">Jawaban Kuesioner</p>
                            <div class="border border-gray-200 dark:border-neutral-600 rounded-lg overflow-hidden">
                                <div class="max-h-96 overflow-y-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-600">
                                        <thead class="bg-gray-50 dark:bg-neutral-700/50 sticky top-0">
                                            <tr>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase">Kategori</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase">Pertanyaan</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase">Jawaban</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-neutral-800 divide-y divide-gray-200 dark:divide-neutral-600">
                                            @foreach($consultation->jawabanKonsultasis as $jawaban)
                                                <tr>
                                                    <td class="px-4 py-3 text-xs text-gray-500 dark:text-neutral-400">
                                                        {{ ucfirst($jawaban->opsiJawaban->pertanyaan->kategori->tipe ?? 'N/A') }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-neutral-200">
                                                        {{ Str::limit($jawaban->opsiJawaban->pertanyaan->teks_pertanyaan ?? 'N/A', 60) }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $jawaban->opsiJawaban->teks_jawaban }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-neutral-600">
                    <button wire:click="backToHistory"
                            class="px-6 py-2.5 bg-gray-200 dark:bg-neutral-600 hover:bg-gray-300 dark:hover:bg-neutral-500 text-gray-800 dark:text-white rounded-lg font-medium transition-colors">
                        Kembali ke Histori
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
