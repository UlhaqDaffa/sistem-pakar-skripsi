<?php

use Livewire\Volt\Component;
use App\Models\Konsultasi;

new class extends Component {
    public ?Konsultasi $consultation = null;

    public function mount($consultation = null): void
    {
        if ($consultation instanceof Konsultasi) {
            $this->consultation = $consultation;
        } elseif (is_numeric($consultation)) {
            $this->consultation = Konsultasi::where('user_id', auth()->id())
                ->where('status', 'selesai')
                ->with(['areaRisetFinal', 'hasilMinat', 'hasilAkademik', 'jawabanKonsultasis.opsiJawaban.pertanyaan.kategori', 'nilaiMataKuliah.mataKuliahKunci'])
                ->findOrFail($consultation);
        }
    }

    public function backToHistory(): void
    {
        $this->redirect(route('riwayat.index'), navigate: true);
    }

}; ?>

@if(!$consultation)
    <div class="grid h-full flex-1 grid-cols-1 gap-4">
        <div class="bg-white/30 dark:bg-neutral-800/30 backdrop-blur-lg border border-white/40 dark:border-white/10 p-6 rounded-xl shadow-md">
            <div class="text-center">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Konsultasi Tidak Ditemukan</h2>
                <p class="text-gray-600 dark:text-neutral-300 mb-6">
                    Konsultasi yang Anda cari tidak ditemukan atau belum selesai.
                </p>
                <x-button-primary-lg wire:click="backToHistory" wire:navigate>
                    Kembali ke Riwayat
                </x-button-primary-lg>
            </div>
        </div>
    </div>
@else
    <div class="grid h-full flex-1 grid-cols-1 gap-4">
        <!-- Main Consultation Detail Card -->
        <div class="bg-white/30 dark:bg-neutral-800/30 backdrop-blur-lg border border-white/40 dark:border-white/10 p-6 md:p-8 rounded-xl shadow-md flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6 flex-shrink-0">
                <div class="flex items-center gap-4">
                    <button wire:click="backToHistory" title="Kembali ke Riwayat" class="flex-shrink-0 p-2 rounded-full text-gray-500 dark:text-neutral-400 hover:bg-gray-500/10 dark:hover:bg-neutral-700/50 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Detail Konsultasi</h2>
                </div>
                <a href="{{ route('ekspor.pdf', ['konsultasi' => $consultation->id]) }}" class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors duration-200">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Ekspor PDF
                </a>
            </div>

            <!-- Consultation Details -->
            <div class="flex-grow my-4 space-y-6">
                <!-- Tanggal & Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Tanggal Konsultasi</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $consultation->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Status</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mt-1 {{ $consultation->status === 'selesai' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' }}">
                            {{ ucfirst($consultation->status) }}
                        </span>
                    </div>
                </div>

                <!-- Hasil Rekomendasi Final -->
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
                                <div class="bg-gray-50 dark:bg-neutral-800/50 p-3 rounded-lg">
                                    <p class="text-xs font-medium text-gray-500 dark:text-neutral-400">{{ $nilai->mataKuliahKunci->nama_mata_kuliah }}</p>
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
                        <div class="border border-gray-200/80 dark:border-neutral-700/80 rounded-lg overflow-hidden">
                            <div class="max-h-96 overflow-y-auto">
                                <table class="min-w-full divide-y divide-gray-200/80 dark:divide-neutral-700/80">
                                    <thead class="bg-gray-50/50 dark:bg-neutral-800/50 sticky top-0">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Kategori</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Pertanyaan</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Jawaban</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white/50 dark:bg-neutral-900/50 divide-y divide-gray-200/50 dark:divide-neutral-700/50">
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

            <!-- Action Buttons -->
            <div class="flex-shrink-0 flex justify-between items-center pt-6 mt-6 border-t border-gray-200/80 dark:border-neutral-700/80">
                <x-button-secondary-lg wire:click="backToHistory" wire:navigate>
                    Kembali
                </x-button-secondary-lg>
                <a href="{{ route('ekspor.pdf', ['konsultasi' => $consultation->id]) }}" class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors duration-200 font-medium">
                    <svg class="h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Ekspor ke PDF
                </a>
            </div>
        </div>
    </div>
@endif
