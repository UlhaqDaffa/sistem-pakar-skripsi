<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Collection;
use App\Models\Konsultasi;
use App\Models\JawabanKonsultasi;
use App\Models\KategoriPertanyaan;
use App\Models\Pertanyaan;
use App\Models\OpsiJawaban;

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

    public function mount(): void
    {
        $this->konsultasi = Konsultasi::create(['user_id' => auth()->id()]);
        $this->pertanyaanSekarang = Pertanyaan::where('is_start_point', true)->with('opsiJawaban', 'kategori')->first();

        if (!$this->pertanyaanSekarang) {
            // Handle jika tidak ada pertanyaan awal, mungkin redirect atau tampilkan error
            $this->finishConsultation();
        }
    }

    public function pilihJawaban(int $opsiJawabanId): void
    {
        // 1. Simpan jawaban
        JawabanKonsultasi::create([
            'konsultasi_id' => $this->konsultasi->id,
            'opsi_jawaban_id' => $opsiJawabanId,
        ]);

        $opsiTerpilih = OpsiJawaban::find($opsiJawabanId);

        // 2. Tambahkan ke ringkasan jawaban di UI
        $this->jawabanUser[] = [
            'pertanyaan' => $this->pertanyaanSekarang->teks_pertanyaan,
            'jawaban' => $opsiTerpilih->teks_jawaban,
        ];

        // 3. Tentukan langkah selanjutnya berdasarkan kategori pertanyaan saat ini
        $kodeKategori = $this->pertanyaanSekarang->kategori->kode_kategori;

        if ($kodeKategori === 'UMUM') {
            $this->handleJawabanUmum($opsiTerpilih);
        } elseif ($kodeKategori === 'MINAT') {
            $this->handleJawabanMinat($opsiTerpilih);
        } elseif ($kodeKategori === 'ASESMEN') {
            $this->handleJawabanAsesmen();
        }
    }

    private function handleJawabanUmum(OpsiJawaban $opsi): void
    {
        // $opsi->kode_jawaban is 'ARKETIPE_CREATOR', 'ARKETIPE_ANALIS', etc.
        $this->archetype = str_replace('ARKETIPE_', '', $opsi->kode_jawaban);

        // Cari pertanyaan minat yang sesuai
        $nextKodePertanyaan = 'MINAT_' . $this->archetype . '_01';
        $this->pertanyaanSekarang = Pertanyaan::where('kode_pertanyaan', $nextKodePertanyaan)->with('opsiJawaban', 'kategori')->first();
        $this->progress = 33;

        if (!$this->pertanyaanSekarang) {
            $this->finishConsultation();
        }
    }

    private function handleJawabanMinat(OpsiJawaban $opsi): void
    {
        // $opsi->kode_jawaban is 'MINAT_WEB_DEV', 'MINAT_MOBILE_DEV', etc.
        $this->minat = str_replace('MINAT_', '', $opsi->kode_jawaban);

        // Cari semua pertanyaan asesmen yang sesuai
        $asesmenKode = 'ASESMEN_' . $this->minat . '_%';
        $this->antrianAsesmen = Pertanyaan::where('kode_pertanyaan', 'like', $asesmenKode)
            ->with('opsiJawaban', 'kategori')
            ->orderBy('id')
            ->get()->all();

        if (!empty($this->antrianAsesmen)) {
            $this->asesmenIndex = 0;
            $this->pertanyaanSekarang = $this->antrianAsesmen[$this->asesmenIndex];
            $this->progress = 66;
        } else {
            // Jika tidak ada pertanyaan asesmen, langsung selesaikan
            $this->finishConsultation();
        }
    }

    private function handleJawabanAsesmen(): void
    {
        $this->asesmenIndex++;
        if ($this->asesmenIndex < count($this->antrianAsesmen)) {
            $this->pertanyaanSekarang = $this->antrianAsesmen[$this->asesmenIndex];
            // Update progress di dalam tahap asesmen
            $totalAsesmen = count($this->antrianAsesmen);
            $this->progress = 66 + (int)(($this->asesmenIndex / $totalAsesmen) * 34);
        } else {
            $this->finishConsultation();
        }
    }

    public function finishConsultation(): void
    {
        $this->progress = 100;
        $this->pertanyaanSekarang = null; // Tidak ada pertanyaan lagi

        $this->konsultasi->status = 'selesai';
        // Di sini Anda bisa menambahkan logika untuk menganalisis jawaban dan menyimpan hasil
        // $this->konsultasi->hasil_minat_id = ...
        $this->konsultasi->save();

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

        @if($this->pertanyaanSekarang)
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
