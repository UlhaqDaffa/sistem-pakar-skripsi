<?php

use Livewire\Volt\Component;
use App\Models\Konsultasi;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function getConsultationsProperty()
    {
        return Konsultasi::where('user_id', auth()->id())
            ->where('status', 'selesai')
            ->with(['areaRisetFinal', 'hasilMinat', 'hasilAkademik'])
            ->latest()
            ->paginate(6);
    }

    public function viewDetails(int $consultationId): void
    {
        $this->redirect(route('riwayat.show', $consultationId), navigate: true);
    }
}; ?>

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
        <div class="flex-grow overflow-y-auto mb-4">
            @if($this->consultations->count() > 0)
                <ul role="list" class="divide-y divide-gray-200 dark:divide-neutral-700">
                    @foreach($this->consultations as $consultation)
                        <li class="py-4 flex justify-between items-center">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $consultation->areaRisetFinal?->nama_area ?? 'Belum ada hasil' }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">
                                    {{ $consultation->created_at->format('d M Y H:i') }}
                                </p>
                                @if($consultation->hasilMinat || $consultation->hasilAkademik)
                                    <div class="flex gap-2 mt-2">
                                        @if($consultation->hasilMinat)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                Minat: {{ $consultation->hasilMinat->nama_area }}
                                            </span>
                                        @endif
                                        @if($consultation->hasilAkademik)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                                Akademik: {{ $consultation->hasilAkademik->nama_area }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <button wire:click="viewDetails({{ $consultation->id }})" 
                                    class="ml-4 text-primary hover:text-primary/80 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary/50 rounded-md px-3 py-1 transition-colors duration-200">
                                Detail &rarr;
                            </button>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="flex flex-col items-center justify-center text-center h-full">
                    <svg class="h-16 w-16 text-gray-400 dark:text-neutral-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    <p class="text-gray-500 dark:text-neutral-400 mb-4">Belum ada riwayat konsultasi yang tersimpan.</p>
                    <a href="{{ route('konsultasi.starter') }}" class="text-primary hover:text-primary/80 dark:text-blue-400 dark:hover:text-blue-300 font-medium" wire:navigate>
                        Mulai Konsultasi Sekarang
                    </a>
                </div>
            @endif
        </div>
        
        <!-- Pagination - Right Bottom -->
        @if($this->consultations->count() > 0 && $this->consultations->hasPages())
            <div class="flex justify-end mt-4 pt-4 border-t border-gray-200 dark:border-neutral-700 flex-shrink-0">
                {{ $this->consultations->links() }}
            </div>
        @endif
    </div>
</div>
