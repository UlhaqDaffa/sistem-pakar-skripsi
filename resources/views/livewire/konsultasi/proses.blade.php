<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use App\Models\Konsultasi;
use App\Models\Pertanyaan;
use App\Models\OpsiJawaban;
use App\Models\OpsiJawabanTemplateItem;
use App\Services\KonsultasiService;

new class extends Component {
    public ?Konsultasi $konsultasi;
    public ?Pertanyaan $pertanyaanSekarang = null;
    public array $jawabanUser = [];
    public int $progress = 0;

    // State
    public ?string $archetype = null;
    public ?string $minat = null;
    public array $antrianAsesmen = [];
    public int $asesmenIndex = 0;
    public bool $showNilaiForm = false;
    public array $nilaiMataKuliah = [
        'algoritma' => '',
        'pemrograman' => '',
        'basis_data' => '',
        'kecerdasan_buatan' => '',
    ];

    public function getNilaiOptions(): array
    {
        return [
            'A' => 'A (Sangat Baik)',
            'B' => 'B (Baik)',
            'C' => 'C (Cukup)',
            'D' => 'D (Kurang)',
            'E' => 'E (Sangat Kurang)',
        ];
    }

    public function mount(): void
    {
        $konsultasiService = app(KonsultasiService::class);
        $this->konsultasi = $konsultasiService->start();
        $this->pertanyaanSekarang = $konsultasiService->getStartQuestion();

        if (!$this->pertanyaanSekarang) {
            $this->finishConsultation();
        }
    }

    public function pilihJawaban(int $opsiJawabanId): void
    {
        // Coba cari sebagai template item dulu (untuk pertanyaan baru), lalu fallback ke OpsiJawaban (untuk data historis)
        $opsiTerpilih = OpsiJawabanTemplateItem::find($opsiJawabanId);
        $teksJawaban = null;

        if ($opsiTerpilih) {
            $teksJawaban = $opsiTerpilih->teks_jawaban;
        } else {
            $opsiTerpilih = OpsiJawaban::find($opsiJawabanId);
            if ($opsiTerpilih) {
                $teksJawaban = $opsiTerpilih->teks_jawaban;
            }
        }

        if (!$opsiTerpilih || !$teksJawaban) {
            return;
        }

        // Add to UI summary before changing the question
        $this->jawabanUser[] = [
            'pertanyaan' => $this->pertanyaanSekarang->teks_pertanyaan,
            'jawaban' => $teksJawaban,
        ];

        $currentState = [
            'archetype' => $this->archetype,
            'minat' => $this->minat,
            'antrianAsesmen' => $this->antrianAsesmen,
            'asesmenIndex' => $this->asesmenIndex,
        ];

        $newState = app(KonsultasiService::class)->processAnswer($this->konsultasi, $this->pertanyaanSekarang, $opsiJawabanId, $currentState);

        // Update component state from the service's response
        foreach ($newState as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        if (!$this->pertanyaanSekarang && !$this->showNilaiForm) {
            $this->finishConsultation();
        }
    }

    public function simpanNilai(): void
    {
        $this->validate([
            'nilaiMataKuliah.algoritma' => 'required|in:A,B,C,D,E',
            'nilaiMataKuliah.pemrograman' => 'required|in:A,B,C,D,E',
            'nilaiMataKuliah.basis_data' => 'required|in:A,B,C,D,E',
            'nilaiMataKuliah.kecerdasan_buatan' => 'required|in:A,B,C,D,E',
        ], [
            'nilaiMataKuliah.*.required' => 'Nilai :attribute harus diisi',
            'nilaiMataKuliah.*.in' => 'Nilai :attribute harus berupa A, B, C, D, atau E',
        ]);

        // Simpan nilai mata kuliah
        app(KonsultasiService::class)->saveNilaiMataKuliah($this->konsultasi, $this->nilaiMataKuliah);

        // Lanjut ke finish
        $this->finishConsultation();
    }

    public function finishConsultation(): void
    {
        $this->progress = 100;
        $this->pertanyaanSekarang = null;
        app(KonsultasiService::class)->finish($this->konsultasi);

        $this->redirect(route('konsultasi.hasil', ['konsultasi' => $this->konsultasi->id]), navigate: true);
    }

    public function previousStep(): void
    {
        $this->konsultasi->delete();
        $this->redirect(route('konsultasi.starter'), navigate: true);
    }

    public function restart(): void
    {
        $this->konsultasi->delete();
        $this->redirect(route('konsultasi.starter'), navigate: true);
    }

    #[Computed]
    public function tahapSekarang(): array
    {
        if ($this->showNilaiForm) {
            return [
                'nama' => 'Tahap 4: Input Nilai Mata Kuliah',
                'deskripsi' => 'Masukkan nilai untuk mata kuliah kunci yang telah Anda ambil.',
                'pertanyaan_ke' => '',
                'total_pertanyaan' => '',
            ];
        }

        if (!$this->pertanyaanSekarang) {
            return [
                'nama' => 'Menyelesaikan Konsultasi',
                'deskripsi' => 'Hasil Anda sedang diproses.',
                'pertanyaan_ke' => '',
                'total_pertanyaan' => '',
            ];
        }

        $kategori = $this->pertanyaanSekarang->kategori;
        $nama = "Tahap " . ($kategori->tipe === 'umum' ? 1 : ($kategori->tipe === 'minat' ? 2 : 3)) . ": " . $kategori->nama_kategori;
        $deskripsi = $kategori->deskripsi;
        $pertanyaan_ke = 1;
        $total_pertanyaan = 1;

        if ($kategori->tipe === 'asesmen') {
            if ($this->minat) {
                $nama_minat = str_replace('_', ' ', $this->minat);
                $nama_minat = ucwords(strtolower($nama_minat));
                $nama = "Tahap 3: Asesmen {$nama_minat}";
                $deskripsi = "Mengukur pemahaman Anda di bidang {$nama_minat}.";
            }
            $pertanyaan_ke = $this->asesmenIndex + 1;
            $total_pertanyaan = count($this->antrianAsesmen);
        }

        return [
            'nama' => $nama,
            'deskripsi' => $deskripsi,
            'pertanyaan_ke' => $pertanyaan_ke,
            'total_pertanyaan' => $total_pertanyaan,
        ];
    }
}; ?>

<!-- Main Consultation View -->
<div class="grid h-full flex-1 grid-cols-1 gap-4 lg:grid-cols-4">

    <!-- Main Consultation Card -->
    <div class="lg:col-span-3 bg-white/30 dark:bg-neutral-800/30 backdrop-blur-lg border border-white/40 dark:border-white/10 p-6 rounded-xl shadow-md flex flex-col">

        <!-- Header & Progress -->
        <div class="flex-shrink-0 mb-6">
            <div class="flex items-center gap-4">
                <button wire:click="previousStep" title="Kembali" class="flex-shrink-0 p-2 rounded-full text-gray-500 dark:text-neutral-400 hover:bg-gray-500/10 dark:hover:bg-neutral-700/50 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $this->tahapSekarang['nama'] }}</h2>
                    <p class="text-gray-600 dark:text-neutral-300 mt-1">{{ $this->tahapSekarang['deskripsi'] }}</p>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mt-4">
                <div class="flex justify-between mb-1">
                    <span class="text-base font-medium text-primary dark:text-blue-400">Proses Konsultasi</span>
                    @if($this->tahapSekarang['total_pertanyaan'] > 0)
                        <span class="text-sm font-medium text-primary dark:text-blue-400">
                            Pertanyaan {{ $this->tahapSekarang['pertanyaan_ke'] }} dari {{ $this->tahapSekarang['total_pertanyaan'] }}
                        </span>
                    @endif
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                    <div class="bg-primary h-2.5 rounded-full transition-all duration-500" style="width: {{ $this->progress }}%"></div>
                </div>
            </div>
        </div>

        @if($this->showNilaiForm)
            <!-- Form Input Nilai Mata Kuliah -->
            <div class="flex-grow flex flex-col items-center justify-center px-4">
                <h3 class="text-xl md:text-3xl font-medium text-gray-900 dark:text-white mb-6">Masukkan Nilai Mata Kuliah</h3>
                <p class="text-gray-600 dark:text-neutral-300 mb-8 max-w-2xl text-center">
                    Silakan pilih nilai untuk mata kuliah kunci berikut (skala A-E):
                </p>

                <form wire:submit="simpanNilai" class="w-full max-w-2xl space-y-6">
                    <div>
                        <label for="algoritma" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                            Algoritma & Struktur Data
                        </label>
                        <flux:select wire:model="nilaiMataKuliah.algoritma" id="algoritma" required class="w-full">
                            <option value="">Pilih Nilai</option>
                            @foreach($this->getNilaiOptions() as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </flux:select>
                        @error('nilaiMataKuliah.algoritma')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="pemrograman" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                            Pemrograman
                        </label>
                        <flux:select wire:model="nilaiMataKuliah.pemrograman" id="pemrograman" required class="w-full">
                            <option value="">Pilih Nilai</option>
                            @foreach($this->getNilaiOptions() as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </flux:select>
                        @error('nilaiMataKuliah.pemrograman')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="basis_data" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                            Basis Data
                        </label>
                        <flux:select wire:model="nilaiMataKuliah.basis_data" id="basis_data" required class="w-full">
                            <option value="">Pilih Nilai</option>
                            @foreach($this->getNilaiOptions() as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </flux:select>
                        @error('nilaiMataKuliah.basis_data')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="kecerdasan_buatan" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                            Kecerdasan Buatan
                        </label>
                        <flux:select wire:model="nilaiMataKuliah.kecerdasan_buatan" id="kecerdasan_buatan" required class="w-full">
                            <option value="">Pilih Nilai</option>
                            @foreach($this->getNilaiOptions() as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </flux:select>
                        @error('nilaiMataKuliah.kecerdasan_buatan')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end pt-4">
                        <flux:button type="submit" variant="primary" class="w-full sm:w-auto">
                            Simpan dan Lanjutkan
                        </flux:button>
                    </div>
                </form>
            </div>
        @elseif($this->pertanyaanSekarang)
            <!-- Question Area -->
            <div class="flex-grow flex flex-col items-center justify-center text-center px-4" wire:key="pertanyaan-{{ $this->pertanyaanSekarang->id }}">
                <h3 class="text-xl md:text-3xl font-medium text-gray-900 dark:text-white">{{ $this->pertanyaanSekarang->teks_pertanyaan }}</h3>

                <!-- Answer Options -->
                <div class="mt-8 flex flex-wrap justify-center items-center gap-4">
                    @foreach($this->pertanyaanSekarang->opsiJawaban as $opsi)
                        <button wire:click="pilihJawaban({{ $opsi->id }})" wire:key="jawaban-{{ $opsi->id }}" class="px-6 py-3 text-base font-semibold rounded-lg bg-primary text-white hover:bg-primary/90 focus:outline-none transition-all duration-200 hover:ring-2 hover:ring-primary/70 dark:hover:ring-offset-neutral-800">
                            {{ $opsi->teks_jawaban }}
                        </button>
                    @endforeach
                </div>
            </div>
        @else
            <!-- All questions answered -->
            <div class="flex-grow flex flex-col items-center justify-center text-center px-4">
                <h3 class="text-xl md:text-3xl font-medium text-gray-900 dark:text-white">Terima kasih!</h3>
                <p class="text-gray-500 dark:text-neutral-400 mt-2 max-w-2xl">Anda telah menyelesaikan semua pertanyaan. Hasil Anda sedang diproses.</p>
            </div>
        @endif

        <!-- Footer Spacer -->
        <div class="flex-shrink-0 mt-6 h-[38px]"></div>
    </div>

    <!-- Summary Card -->
    <div class="lg:col-span-1 bg-white/30 dark:bg-neutral-800/30 backdrop-blur-lg border border-white/40 dark:border-white/10 p-6 rounded-xl shadow-md flex flex-col">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex-shrink-0">Ringkasan Jawaban</h3>
        <div class="mt-4 flex-grow overflow-y-auto">
            @if(empty($jawabanUser))
                <ul class="space-y-3">
                    <li class="flex justify-between items-center text-sm text-gray-400 dark:text-neutral-500">
                        <span>Belum ada jawaban yang diberikan.</span>
                    </li>
                </ul>
            @else
                <ul class="space-y-3">
                    @foreach($jawabanUser as $item)
                        <li class="text-sm text-gray-700 dark:text-neutral-300">
                            <span class="font-medium">{{ Str::limit($item['pertanyaan'], 40) }}:</span>
                            <span class="block font-bold text-primary dark:text-blue-400">{{ $item['jawaban'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        <div class="mt-4 flex-shrink-0">
            <button wire:click="restart" class="w-full px-6 py-2 font-medium rounded-md text-red-600 bg-red-500/10 hover:bg-red-500/20 transition-all duration-200 hover:ring-2 hover:ring-red-500/30">
                Ulangi Konsultasi
            </button>
        </div>
    </div>

</div>