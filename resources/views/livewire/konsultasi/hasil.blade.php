<?php

use Livewire\Volt\Component;
use Illuminate\Support\Collection;

new class extends Component {
    use \Livewire\WithPagination;

    public Collection $questions;
    public array $answers;
    public string $conclusion;
    public array $recommendations;

    public function mount(): void
    {
        // Retrieve answers from the session, default to empty array if not found.
        $this->answers = session('consultation_answers', []);

        // In a real app, this would come from a service or database.
        $this->questions = collect([
            ['id' => 1, 'text' => 'Apakah Anda sering merasa kesulitan untuk fokus saat belajar?'],
            ['id' => 2, 'text' => 'Apakah Anda merasa cemas berlebihan saat menghadapi ujian?'],
            ['id' => 3, 'text' => 'Apakah Anda sering menunda-nunda pekerjaan atau tugas sekolah?'],
            ['id' => 4, 'text' => 'Apakah Anda merasa kurang termotivasi untuk belajar?'],
            ['id' => 5, 'text' => 'Apakah Anda sering merasa lelah atau kurang energi?'],
        ]);

        $this->generateResult();
    }

    public function generateResult(): void
    {
        if (empty($this->answers)) {
            $this->conclusion = 'Anda belum menyelesaikan sesi konsultasi.';
            $this->recommendations = ['Silakan mulai sesi konsultasi untuk mendapatkan hasil.'];
            return;
        }

        $yesCount = count(array_filter($this->answers));

        if ($yesCount >= 4) {
            $this->conclusion = 'Berdasarkan jawaban Anda, tampaknya Anda mengalami beberapa tantangan signifikan terkait fokus dan motivasi belajar.';
            $this->recommendations = [
                'Sangat disarankan untuk berbicara dengan konselor sekolah atau profesional.',
                'Praktikkan teknik relaksasi seperti meditasi untuk mengelola stres.',
                'Buat jadwal belajar yang terstruktur dan realistis.',
            ];
        } elseif ($yesCount >= 2) {
            $this->conclusion = 'Anda menunjukkan beberapa gejala kesulitan belajar. Ada baiknya untuk mulai lebih memperhatikan pola belajar Anda.';
            $this->recommendations = [
                'Coba gunakan teknik manajemen waktu seperti metode Pomodoro.',
                'Pastikan Anda mendapatkan istirahat yang cukup di antara sesi belajar.',
                'Identifikasi dan kurangi distraksi di lingkungan belajar Anda.',
            ];
        } else {
            $this->conclusion = 'Gejala kesulitan belajar Anda tampak ringan. Terus pertahankan kebiasaan baik Anda!';
            $this->recommendations = [
                'Pertahankan pola tidur yang teratur untuk menjaga energi.',
                'Tetap luangkan waktu untuk hobi dan aktivitas yang Anda nikmati.',
                'Evaluasi secara berkala metode belajar yang paling efektif untuk Anda.',
            ];
        }
    }

    public function getQuestionText(int $step): string
    {
        return $this->questions->firstWhere('id', $step)['text'] ?? 'Pertanyaan tidak ditemukan';
    }

};

?>

<div class="grid h-full flex-1 grid-cols-1 gap-4 lg:grid-cols-4">

    <!-- Main Result Card -->
    <div class="lg:col-span-3 bg-white/30 dark:bg-neutral-800/30 backdrop-blur-lg border border-white/40 dark:border-white/10 p-6 md:p-8 rounded-xl shadow-md flex flex-col">

        <!-- Header -->
        <div class="text-center flex-shrink-0">
            <svg class="h-16 w-16 text-primary mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Hasil Konsultasi Anda</h1>
            <p class="mt-2 max-w-2xl mx-auto text-gray-600 dark:text-neutral-300">
                Berikut adalah ringkasan dan beberapa rekomendasi berdasarkan jawaban yang Anda berikan.
            </p>
        </div>

        <!-- Content Area -->
        <div class="flex-grow my-8">
            <!-- Main Conclusion -->
            <div class="bg-primary/10 dark:bg-primary/20 border-l-4 border-primary dark:border-blue-400 p-4 rounded-r-lg mb-6">
                <p class="font-semibold text-primary dark:text-blue-300">Kesimpulan Utama:</p>
                <p class="text-gray-800 dark:text-neutral-200">{{ $conclusion }}</p>
            </div>

            <!-- Recommendations -->
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Rekomendasi</h2>
                <ul class="space-y-2 list-disc list-inside text-gray-700 dark:text-neutral-300">
                    @foreach($recommendations as $recommendation)
                        <li>{{ $recommendation }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex-shrink-0 flex flex-col sm:flex-row justify-center items-center gap-4 pt-6 border-t border-gray-200/80 dark:border-neutral-700/80">
            <x-button-primary-lg href="{{ route('dashboard') }}" wire:navigate>
                Kembali ke Dashboard
            </x-button-primary-lg>

            <x-button-secondary-lg>
                Ekspor Hasil (PDF)
            </x-button-secondary-lg>

            <livewire:konsultasi.hasil-restart-modal/>
        </div>
    </div>

    <!-- Detailed Answers Card -->
    <div class="lg:col-span-1 bg-white/30 dark:bg-neutral-800/30 backdrop-blur-lg border border-white/40 dark:border-white/10 p-6 rounded-xl shadow-md flex flex-col">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex-shrink-0">Rincian Jawaban</h3>
        <div class="mt-4 flex-grow overflow-y-auto">
            <div class="border border-gray-200/80 dark:border-neutral-700/80 rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200/80 dark:divide-neutral-700/80">
                    <thead class="bg-gray-50/50 dark:bg-neutral-800/50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">#</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Jawaban</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white/50 dark:bg-neutral-900/50 divide-y divide-gray-200/50 dark:divide-neutral-700/50">
                        @if(!empty($answers))
                            @foreach($answers as $step => $answer)
                                <tr>
                                    <td class="px-4 py-3 whitespace-normal text-sm text-gray-800 dark:text-neutral-200 font-bold">Q{{ $step }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-bold">
                                        <span class="{{ $answer ? 'text-green-500' : 'text-red-500' }}">
                                            {{ $answer ? 'Ya' : 'Tidak' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-neutral-400">Tidak ada data.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
