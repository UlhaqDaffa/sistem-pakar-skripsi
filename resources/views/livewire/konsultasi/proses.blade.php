<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Collection;
use App\Models\Konsultasi;
use App\Models\KonsultasiDetail;
use App\Models\KategoriPertanyaan;
use App\Models\Pertanyaan;
use App\Models\OpsiJawaban;

new class extends Component {
    public ?Konsultasi $konsultasi;
    public Collection $kategoriPertanyaan;
    public Collection $semuaPertanyaan;
    public array $jawabanUser = [];

    public int $tahapIndex = 0;
    public int $pertanyaanIndex = 0;
    public int $iteration = 0;
    public function mount(): void
    {
        $this->konsultasi = Konsultasi::create(['user_id' => auth()->id()]);
        $this->kategoriPertanyaan = KategoriPertanyaan::orderBy('id')->get();
        $this->pertanyaanIndex = 0;
        $this->loadTahap();
    }

    public function loadTahap(): void
    {
        if ($this->tahapIndex >= $this->kategoriPertanyaan->count()) {
            $this->finishConsultation();
            return;
        }
        $kategoriId = $this->kategoriPertanyaan[$this->tahapIndex]->id;
        $this->semuaPertanyaan = Pertanyaan::where('kategori_id', $kategoriId)
            ->with('opsiJawaban')
            ->orderBy('urutan')
            ->get();
    }

    public function pilihJawaban(int $opsiJawabanId): void
    {
        KonsultasiDetail::create([
            'konsultasi_id' => $this->konsultasi->id,
            'pertanyaan_id' => $this->pertanyaanSekarang()->id,
            'opsi_jawaban_id' => $opsiJawabanId
        ]);

        $this->jawabanUser[] = [
            'pertanyaan' => $this->pertanyaanSekarang()->teks_pertanyaan,
            'jawaban' => OpsiJawaban::find($opsiJawabanId)->teks_jawaban,
        ];

        $this->next();
    }

    private function next(): void
    {
        $this->pertanyaanIndex++;
        if ($this->pertanyaanIndex >= $this->semuaPertanyaan->count()) {
            $this->tahapIndex++;
            $this->pertanyaanIndex = 0;
            $this->loadTahap();
        }
    }

    public function finishConsultation(): void
    {
        // Tambahin logika untuk proses model decision tree disini
        $this->konsultasi->update(['kesimpulan' => 'Menunggu proses analisis...']);
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
    public function kategoriSekarang(): ?KategoriPertanyaan
    {
        return $this->kategoriPertanyaan->get($this->tahapIndex);
    }

    public function pertanyaanSekarang(): ?Pertanyaan
    {
        return $this->semuaPertanyaan->get($this->pertanyaanIndex);
    }

    #[Computed]
    public function progress(): int
    {
        $totalKategori = $this->kategoriPertanyaan->count();
        if ($totalKategori === 0) return 0;
        $progressTahap = ($this->tahapIndex / $totalKategori) * 100;
        $totalPertanyaanDiTahap = $this->semuaPertanyaan->count();
        if ($totalPertanyaanDiTahap > 0) {
            $progressDiTahap = ($this->pertanyaanIndex / $totalPertanyaanDiTahap) * (100 / $totalKategori);
            $progressTahap += $progressDiTahap;
        }
        return min(100, (int)$progressTahap);
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
                    @if($this->kategoriSekarang)
                        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Tahap {{ $this->tahapIndex + 1 }}: {{ $this->kategoriSekarang->nama_kategori }}</h2>
                        <p class="text-gray-600 dark:text-neutral-300 mt-1">{{ $this->kategoriSekarang->deskripsi }}</p>
                    @else
                        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Menyelesaikan Konsultasi</h2>
                        <p class="text-gray-600 dark:text-neutral-300 mt-1">Hasil Anda sedang diproses.</p>
                    @endif
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mt-4">
                <div class="flex justify-between mb-1">
                    <span class="text-base font-medium text-primary dark:text-blue-400">Proses Konsultasi</span>
                    @if($this->kategoriSekarang && $this->semuaPertanyaan->count() > 0)
                        <span class="text-sm font-medium text-primary dark:text-blue-400">Pertanyaan {{ $this->pertanyaanIndex + 1 }} dari {{ $this->semuaPertanyaan->count() }}</span>
                    @endif
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                    <div class="bg-primary h-2.5 rounded-full transition-all duration-500" style="width: {{ $this->progress }}%"></div>
                </div>
            </div>
        </div>

        @if($this->pertanyaanSekarang())
            <!-- Question Area -->
            <div class="flex-grow flex flex-col items-center justify-center text-center px-4" wire:key="tahap-{{ $this->tahapIndex }}-pertanyaan-{{ $this->pertanyaanIndex }}">
                <h3 class="text-xl md:text-3xl font-medium text-gray-900 dark:text-white">{{ $this->pertanyaanSekarang()->teks_pertanyaan }}</h3>

                <!-- Answer Options -->
                <div class="mt-8 flex flex-wrap justify-center items-center gap-4">

                    @if($this->pertanyaanSekarang()->tipe_jawaban === 'pilihan_ganda' || $this->pertanyaanSekarang()->tipe_jawaban === 'input_nilai')
                        @foreach($this->pertanyaanSekarang()->opsiJawaban as $opsi)
                            <button wire:click="pilihJawaban({{ $opsi->id }})" wire:key="jawaban-{{ $opsi->id }}" class="px-6 py-3 text-base font-semibold rounded-lg bg-primary text-white hover:bg-primary/90 focus:outline-none transition-all duration-200 hover:ring-2 hover:ring-primary/70 dark:hover:ring-offset-neutral-800">
                                {{ $opsi->teks_jawaban }}
                            </button>
                        @endforeach
                    @endif

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
