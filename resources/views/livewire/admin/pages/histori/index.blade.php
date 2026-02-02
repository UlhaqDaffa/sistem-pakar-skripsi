<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Konsultasi;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app-admin')] class extends Component {
    use WithPagination;

    public string $search = '';

    public function getConsultationsProperty()
    {
        $query = Konsultasi::with(['user', 'areaRisetFinal', 'hasilMinat', 'hasilAkademik'])
            ->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('user', function ($userQ) {
                    $userQ->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('areaRisetFinal', function ($areaQ) {
                    $areaQ->where('nama_area', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('hasilMinat', function ($areaQ) {
                    $areaQ->where('nama_area', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('hasilAkademik', function ($areaQ) {
                    $areaQ->where('nama_area', 'like', '%' . $this->search . '%');
                })
                ->orWhere('status', 'like', '%' . $this->search . '%');
            });
        }

        return $query->paginate(10);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

}; ?>

<div>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Histori Konsultasi</h1>
                <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">Daftar seluruh riwayat konsultasi pengguna</p>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-md p-4 border border-gray-200 dark:border-neutral-700">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Cari berdasarkan nama, email, area riset, atau status..."
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-lg border border-gray-200 dark:border-neutral-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-neutral-900 dark:to-neutral-800">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Pengguna</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Rekomendasi Final</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Minat / Akademik</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-neutral-800 divide-y divide-gray-200 dark:divide-neutral-700">
                        @forelse($this->consultations as $consultation)
                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700/50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $consultation->user?->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-neutral-400">{{ $consultation->user?->email ?? '-' }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600 dark:text-neutral-400">{{ $consultation->created_at->format('d M Y H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1.5 inline-flex text-xs font-semibold rounded-full {{ $consultation->status === 'selesai' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' }} shadow-sm">
                                        {{ ucfirst($consultation->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $consultation->areaRisetFinal?->nama_area ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @if($consultation->hasilMinat)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">{{ $consultation->hasilMinat->nama_area }}</span>
                                        @endif
                                        @if($consultation->hasilAkademik)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">{{ $consultation->hasilAkademik->nama_area }}</span>
                                        @endif
                                        @if(!$consultation->hasilMinat && !$consultation->hasilAkademik)
                                            <span class="text-xs text-gray-400 dark:text-neutral-500">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('admin.riwayat-konsultasi.show', $consultation->id) }}"
                                       class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium"
                                       wire:navigate>
                                        Detail &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-neutral-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Belum ada riwayat konsultasi</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($this->consultations->hasPages())
                <div class="px-6 py-4 bg-gray-50 dark:bg-neutral-900 border-t border-gray-200 dark:border-neutral-700">
                    {{ $this->consultations->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
