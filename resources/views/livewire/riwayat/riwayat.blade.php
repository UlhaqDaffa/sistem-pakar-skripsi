<?php

use Livewire\Volt\Component;
use Illuminate\Support\Collection;
// use App\Models\Consultation; // Still include for future real implementation

new class extends Component {
    public Collection $consultations;

    public function mount(): void
    {
        // Dummy data for demonstration purposes
        $this->consultations = collect([
            (object)[
                'id' => 1,
                'conclusion' => 'Berdasarkan jawaban Anda, tampaknya Anda mengalami beberapa tantangan signifikan terkait fokus dan motivasi belajar.',
                'created_at' => now()->subDays(5),
                'recommendations' => json_encode([
                    'Sangat disarankan untuk berbicara dengan konselor sekolah atau profesional.',
                    'Praktikkan teknik relaksasi seperti meditasi untuk mengelola stres.',
                    'Buat jadwal belajar yang terstruktur dan realistis.',
                ]),
                'answers' => json_encode([
                    1 => true,
                    2 => true,
                    3 => false,
                    4 => true,
                    5 => true,
                ]),
            ],
            (object)[
                'id' => 2,
                'conclusion' => 'Anda menunjukkan beberapa gejala kesulitan belajar. Ada baiknya untuk mulai lebih memperhatikan pola belajar Anda.',
                'created_at' => now()->subDays(10),
                'recommendations' => json_encode([
                    'Coba gunakan teknik manajemen waktu seperti metode Pomodoro.',
                    'Pastikan Anda mendapatkan istirahat yang cukup di antara sesi belajar.',
                    'Identifikasi dan kurangi distraksi di lingkungan belajar Anda.',
                ]),
                'answers' => json_encode([
                    1 => true,
                    2 => false,
                    3 => true,
                    4 => false,
                    5 => false,
                ]),
            ],
            (object)[
                'id' => 3,
                'conclusion' => 'Gejala kesulitan belajar Anda tampak ringan. Terus pertahankan kebiasaan baik Anda!',
                'created_at' => now()->subDays(15),
                'recommendations' => json_encode([
                    'Pertahankan pola tidur yang teratur untuk menjaga energi.',
                    'Tetap luangkan waktu untuk hobi dan aktivitas yang Anda nikmati.',
                    'Evaluasi secara berkala metode belajar yang paling efektif untuk Anda.',
                ]),
                'answers' => json_encode([
                    1 => false,
                    2 => false,
                    3 => false,
                    4 => true,
                    5 => false,
                ]),
            ],
        ]);
    }

    public function viewDetails(int $consultationId): void
    {
        // Redirect to a detailed view of the consultation
        $this->redirect(route('riwayat.show', $consultationId), navigate: true);
    }
};

?>

<div class="grid h-full flex-1 grid-cols-1 gap-4">

    <!-- Main History Card -->
    <div class="bg-white/30 dark:bg-neutral-800/30 backdrop-blur-lg border border-white/40 dark:border-white/10 p-6 rounded-xl shadow-md flex flex-col">

        <!-- Header -->
        <div class="flex items-center gap-4 mb-6 flex-shrink-0">
            <svg class="h-8 w-8 text-primary dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Riwayat Konsultasi</h2>
        </div>

        <!-- Consultation List -->
        <div class="flex-grow overflow-y-auto">
            @if($consultations->isEmpty())
                <div class="flex flex-col items-center justify-center text-center h-full">
                    <svg class="h-16 w-16 text-gray-400 dark:text-neutral-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    <p class="text-gray-500 dark:text-neutral-400">Belum ada riwayat konsultasi yang tersimpan.</p>
                    <a href="{{ route('konsultasi.starter') }}" class="mt-4 text-primary hover:text-primary/80 dark:text-blue-400 dark:hover:text-blue-300 font-medium" wire:navigate>
                        Mulai Konsultasi Sekarang
                    </a>
                </div>
            @else
                <ul role="list" class="divide-y divide-gray-200 dark:divide-neutral-700">
                    @foreach($consultations as $consultation)
                        <li class="py-4 flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $consultation->conclusion ?? 'Tidak ada kesimpulan' }}</p>
                                <p class="text-sm text-gray-500 dark:text-neutral-400">{{ $consultation->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <button wire:click="viewDetails({{ $consultation->id }})" class="text-primary hover:text-primary/80 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary/50 rounded-md px-3 py-1 transition-colors duration-200">
                                Detail &rarr;
                            </button>
                        </li>
                    @endforeach
                </ul>
                <!-- Add pagination links here if needed -->
                {{-- <div class="mt-4">
                    {{ $consultations->links() }}
                </div> --}}
            @endif
        </div>
    </div>
</div>
