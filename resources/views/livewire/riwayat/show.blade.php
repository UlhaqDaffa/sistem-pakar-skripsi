<?php

use Livewire\Volt\Component;
// use App\Models\Consultation;

new class extends Component {
    public Consultation $consultation;

    public function mount(?Consultation $consultation = null): void
    {
        if ($consultation) {
            $this->consultation = $consultation;
        } else {
            // Dummy data for demonstration if no consultation is provided
            $this->consultation = (object)[
                'id' => 99,
                'conclusion' => 'Ini adalah kesimpulan dummy untuk konsultasi detail.',
                'created_at' => now(),
                'recommendations' => json_encode([
                    'Rekomendasi dummy 1.',
                    'Rekomendasi dummy 2.',
                    'Rekomendasi dummy 3.',
                ]),
                'answers' => json_encode([
                    1 => true,
                    2 => false,
                    3 => true,
                    4 => true,
                    5 => false,
                ]),
            ];
        }
    }

    public function backToHistory(): void
    {
        $this->redirect(route('riwayat.index'), navigate: true);
    }
};

?>

<div class="grid h-full flex-1 grid-cols-1 gap-4">

    <!-- Main Consultation Detail Card -->
    <div class="bg-white/30 dark:bg-neutral-800/30 backdrop-blur-lg border border-white/40 dark:border-white/10 p-6 md:p-8 rounded-xl shadow-md flex flex-col">

        <!-- Header -->
        <div class="flex items-center gap-4 mb-6 flex-shrink-0">
            <button wire:click="backToHistory" title="Kembali ke Riwayat" class="flex-shrink-0 p-2 rounded-full text-gray-500 dark:text-neutral-400 hover:bg-gray-500/10 dark:hover:bg-neutral-700/50 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Detail Konsultasi</h2>
        </div>

        <!-- Consultation Details -->
        <div class="flex-grow my-4 space-y-4">
            <div>
                <p class="text-lg font-medium text-gray-700 dark:text-neutral-300">Tanggal Konsultasi:</p>
                <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ $consultation->created_at->format('d M Y H:i') }}</p>
            </div>

            <div>
                <p class="text-lg font-medium text-gray-700 dark:text-neutral-300">Kesimpulan:</p>
                <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ $consultation->conclusion ?? 'Tidak ada kesimpulan' }}</p>
            </div>

            @if($consultation->recommendations)
                <div>
                    <p class="text-lg font-medium text-gray-700 dark:text-neutral-300">Rekomendasi:</p>
                    <ul class="list-disc list-inside text-gray-700 dark:text-neutral-300 space-y-1">
                        @foreach(json_decode($consultation->recommendations) as $recommendation)
                            <li>{{ $recommendation }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($consultation->answers)
                <div>
                    <p class="text-lg font-medium text-gray-700 dark:text-neutral-300">Jawaban:</p>
                    <div class="border border-gray-200/80 dark:border-neutral-700/80 rounded-lg overflow-hidden mt-2">
                        <table class="min-w-full divide-y divide-gray-200/80 dark:divide-neutral-700/80">
                            <thead class="bg-gray-50/50 dark:bg-neutral-800/50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Pertanyaan</th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Jawaban</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white/50 dark:bg-neutral-900/50 divide-y divide-gray-200/50 dark:divide-neutral-700/50">
                                @foreach(json_decode($consultation->answers) as $questionId => $answer)
                                    <tr>
                                        <td class="px-4 py-3 whitespace-normal text-sm text-gray-800 dark:text-neutral-200 font-bold">Pertanyaan #{{ $questionId }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-bold">
                                            <span class="{{ $answer ? 'text-green-500' : 'text-red-500' }}">
                                                {{ $answer ? 'Ya' : 'Tidak' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex-shrink-0 flex justify-end pt-6 border-t border-gray-200/80 dark:border-neutral-700/80">
            <x-button-secondary-lg wire:click="backToHistory">
                Kembali
            </x-button-secondary-lg>
        </div>
    </div>
</div>
