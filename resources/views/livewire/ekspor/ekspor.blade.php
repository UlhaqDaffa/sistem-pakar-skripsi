<?php

use Livewire\Volt\Component;
use App\Models\Konsultasi;

new class extends Component {
    public ?int $konsultasiId = null;
    public ?Konsultasi $konsultasi = null;

    public function mount($konsultasi = null): void
    {
        if (is_numeric($konsultasi)) {
            $this->konsultasiId = $konsultasi;
            $this->konsultasi = Konsultasi::where('user_id', auth()->id())
                ->where('status', 'selesai')
                ->with(['areaRisetFinal', 'hasilMinat', 'hasilAkademik', 'jawabanKonsultasis.opsiJawaban.pertanyaan.kategori', 'nilaiMataKuliah.mataKuliahKunci'])
                ->find($konsultasi);
        }
    }

}; ?>

<div class="grid h-full flex-1 grid-cols-1 gap-4">
    <!-- Main Export Card -->
    <div class="bg-white/30 dark:bg-neutral-800/30 backdrop-blur-lg border border-white/40 dark:border-white/10 p-6 rounded-xl shadow-md flex flex-col">
        <!-- Header -->
        <div class="mb-6 flex-shrink-0">
            <svg class="h-16 w-16 text-primary dark:text-blue-400 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
            <h2 class="text-3xl font-semibold text-gray-900 dark:text-white text-center">Ekspor Riwayat Konsultasi</h2>
        </div>

        <!-- Description -->
        <p class="text-gray-600 dark:text-neutral-300 mb-8 max-w-xl mx-auto text-center">
            Anda dapat mengekspor riwayat konsultasi ke dalam format PDF yang mudah dibaca.
            Pilih opsi di bawah ini untuk memulai proses ekspor.
        </p>

        <!-- Export Options -->
        <div class="flex flex-col gap-4 max-w-2xl mx-auto w-full">
            @if($konsultasi)
                <!-- Export Single Consultation -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Ekspor Konsultasi Spesifik</h3>
                    <p class="text-sm text-gray-600 dark:text-neutral-300 mb-4">
                        Ekspor konsultasi yang dipilih: <strong>{{ $konsultasi->areaRisetFinal?->nama_area ?? 'N/A' }}</strong>
                        <br>
                        <span class="text-xs text-gray-500 dark:text-neutral-400">Tanggal: {{ $konsultasi->created_at->format('d M Y, H:i') }}</span>
                    </p>
                    <a href="{{ route('ekspor.pdf', ['konsultasi' => $konsultasi->id]) }}" class="w-full px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors duration-200 font-medium flex items-center justify-center gap-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Ekspor Konsultasi Ini ke PDF
                    </a>
                </div>
            @endif

            <!-- Export All Consultations -->
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Ekspor Semua Konsultasi</h3>
                <p class="text-sm text-gray-600 dark:text-neutral-300 mb-4">
                    Ekspor seluruh riwayat konsultasi Anda ke dalam satu file PDF.
                </p>
                    <a href="{{ route('ekspor.pdf.all') }}" class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 font-medium flex items-center justify-center gap-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Ekspor Semua Konsultasi ke PDF
                    </a>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mt-8 bg-gray-50 dark:bg-neutral-800/50 border border-gray-200 dark:border-neutral-700 rounded-lg p-4 max-w-2xl mx-auto">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-gray-400 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-sm text-gray-600 dark:text-neutral-300">
                    <p class="font-medium mb-1">Catatan:</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>File PDF akan berisi informasi lengkap tentang konsultasi Anda</li>
                        <li>File akan otomatis terunduh setelah proses ekspor selesai</li>
                        <li>Pastikan browser Anda mengizinkan unduhan file</li>
                    </ul>
                </div>
            </div>
        </div>

        @if (session()->has('error'))
            <div class="mt-6 p-3 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 rounded-lg max-w-2xl mx-auto">
                {{ session('error') }}
            </div>
        @endif

        @if (session()->has('message'))
            <div class="mt-6 p-3 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-lg max-w-2xl mx-auto">
                {{ session('message') }}
            </div>
        @endif

        <!-- Back Button -->
        <div class="mt-6 text-center">
            <a href="{{ route('dashboard') }}" class="text-primary hover:text-primary/80 dark:text-blue-400 dark:hover:text-blue-300 font-medium" wire:navigate>
                ← Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
