<?php

use Livewire\Volt\Component;
use Illuminate\Support\Collection;

new class extends Component {
    public Collection $questions;
    public Collection $answers;
    public int $step = 1;

    public function mount(): void
    {
        $this->questions = collect([
            ['id' => 1, 'text' => 'Apakah Anda sering merasa kesulitan untuk fokus saat belajar?', 'description' => 'Ini bisa menjadi indikasi dari beberapa faktor, seperti kelelahan, stres, atau gangguan lainnya.'],
            ['id' => 2, 'text' => 'Apakah Anda merasa cemas berlebihan saat menghadapi ujian?', 'description' => 'Kecemasan dapat mempengaruhi kinerja akademis dan kesehatan mental Anda.'],
            ['id' => 3, 'text' => 'Apakah Anda sering menunda-nunda pekerjaan atau tugas sekolah?', 'description' => 'Prokrastinasi adalah masalah umum yang dapat diatasi dengan strategi yang tepat.'],
            ['id' => 4, 'text' => 'Apakah Anda merasa kurang termotivasi untuk belajar?', 'description' => 'Motivasi adalah kunci keberhasilan akademis, dan ada banyak cara untuk meningkatkannya.'],
            ['id' => 5, 'text' => 'Apakah Anda sering merasa lelah atau kurang energi?', 'description' => 'Kelelahan dapat disebabkan oleh banyak hal, termasuk kurang tidur, stres, dan pola makan.'],
        ]);

        $this->answers = collect();
    }

    public function answer(bool $response): void
    {
        $this->answers->put($this->step, $response);

        if ($this->step >= $this->questions->count()) {
            // All questions answered, redirect to the results page.
            // We can pass the answers via session or query string.
            session()->put('consultation_answers', $this->answers->all());
            $this->redirect(route('konsultasi.hasil'), navigate: true);
            return;
        }

        $this->step++;
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->answers->forget($this->step);
            $this->step--;
        } else {
            $this->redirect(route('konsultasi.starter'), navigate: true);
        }
    }

    public function restart(): void
    {
        $this->step = 1;
        $this->answers = collect();
    }

    public function getCurrentQuestionProperty(): ?array
    {
        return $this->questions->firstWhere('id', $this->step);
    }

    public function getProgressProperty(): int
    {
        // Ensure we don't divide by zero and calculate progress for the current step.
        if ($this->questions->isEmpty()) {
            return 0;
        }
        return (($this->step -1) / $this->questions->count()) * 100;
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
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Mulai Konsultasi</h2>
                    <p class="text-gray-600 dark:text-neutral-300 mt-1">Jawab pertanyaan berikut untuk mendapatkan hasil.</p>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mt-4">
                <div class="flex justify-between mb-1">
                    <span class="text-base font-medium text-primary dark:text-blue-400">Proses Konsultasi</span>
                    <span class="text-sm font-medium text-primary dark:text-blue-400">Langkah {{ $step }} dari {{ $questions->count() }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                    <div class="bg-primary h-2.5 rounded-full transition-all duration-500" style="width: {{ $this->progress }}%"></div>
                </div>
            </div>
        </div>

        @if($this->currentQuestion)
        <!-- Question Area -->
        <div class="flex-grow flex flex-col items-center justify-center text-center px-4" wire:key="question-{{ $this->currentQuestion['id'] }}">
            <h3 class="text-xl md:text-3xl font-medium text-gray-900 dark:text-white">{{ $this->currentQuestion['text'] }}</h3>
            <p class="text-gray-500 dark:text-neutral-400 mt-2 max-w-2xl">{{ $this->currentQuestion['description'] }}</p>

            <!-- Answer Options -->
            <div class="mt-8 flex gap-4">
                <button wire:click="answer(true)" class="px-8 py-3 text-lg font-semibold rounded-lg bg-primary text-white hover:bg-primary/90 focus:outline-none transition-all duration-200 hover:ring-2 hover:ring-primary/70 dark:hover:ring-offset-neutral-800">
                    Ya
                </button>
                <button wire:click="answer(false)" class="px-8 py-3 text-lg font-semibold rounded-lg dark:bg-neutral-700 text-gray-800 dark:text-white hover:bg-gray-300 dark:hover:bg-neutral-600 focus:outline-none transition-all duration-200 hover:ring-2 hover:ring-gray-400 dark:hover:ring-neutral-500 dark:hover:ring-offset-neutral-800">
                    Tidak
                </button>
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
        <div class="flex-shrink-0 mt-6 h-[38px]">
            <!-- This div is a spacer to align the layout with/without the 'Berikutnya' button -->
        </div>
    </div>

    <!-- Summary Card -->
    <div class="lg:col-span-1 bg-white/30 dark:bg-neutral-800/30 backdrop-blur-lg border border-white/40 dark:border-white/10 p-6 rounded-xl shadow-md flex flex-col">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex-shrink-0">Ringkasan Jawaban</h3>
        <div class="mt-4 flex-grow overflow-y-auto">
            @if($answers->isEmpty())
                <ul class="space-y-3">
                    <li class="flex justify-between items-center text-sm text-gray-400 dark:text-neutral-500">
                        <span>Belum ada jawaban yang diberikan.</span>
                    </li>
                </ul>
            @else
                <ul class="space-y-3">
                    @foreach($answers as $step => $answer)
                        <li class="flex justify-between items-center text-sm text-gray-700 dark:text-neutral-300">
                            <span class="font-medium">Pertanyaan #{{ $step }}:</span>
                            <span class="font-bold {{ $answer ? 'text-green-500' : 'text-red-500' }}">
                                {{ $answer ? 'Ya' : 'Tidak' }}
                            </span>
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