<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\PolaJudul;
use App\Models\AreaRiset;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app-admin')] class extends Component {
    use WithPagination;

    public bool $showModal = false;
    public bool $editing = false;
    public ?int $id = null;
    public string $deskripsi = '';
    public string $templateString = '';
    public array $selectedAreaRiset = [];
    public bool $showDeleteModal = false;
    public ?int $deleteId = null;
    public string $search = '';

    public function getPolaJudulsProperty()
    {
        $query = PolaJudul::with(['areaRisets']);
        
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('deskripsi', 'like', '%' . $this->search . '%')
                  ->orWhere('template_string', 'like', '%' . $this->search . '%');
            });
        }
        
        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function getAreaRisetsProperty()
    {
        return AreaRiset::orderBy('nama_area')->get();
    }

    public function openCreateModal(): void
    {
        $this->reset(['id', 'deskripsi', 'templateString', 'selectedAreaRiset']);
        $this->selectedAreaRiset = [];
        $this->editing = false;
        $this->showModal = true;
    }

    public function openEditModal(int $polaJudulId): void
    {
        $polaJudul = PolaJudul::with(['areaRisets'])->findOrFail($polaJudulId);
        $this->id = $polaJudul->id;
        $this->deskripsi = $polaJudul->deskripsi ?? '';
        $this->templateString = $polaJudul->template_string;
        $this->selectedAreaRiset = $polaJudul->areaRisets->pluck('id')->toArray();
        $this->editing = true;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['id', 'deskripsi', 'templateString', 'selectedAreaRiset', 'editing']);
        $this->selectedAreaRiset = [];
    }

    public function save(): void
    {
        $rules = [
            'deskripsi' => 'required|string|max:255',
            'templateString' => 'required|string',
            'selectedAreaRiset' => 'required|array|min:1',
            'selectedAreaRiset.*' => 'exists:area_riset,id',
        ];
        
        $this->validate($rules);
        
        $data = [
            'deskripsi' => $this->deskripsi,
            'template_string' => $this->templateString,
        ];
        
        if ($this->editing) {
            $polaJudul = PolaJudul::findOrFail($this->id);
            $polaJudul->update($data);
        } else {
            $polaJudul = PolaJudul::create($data);
        }
        
        // Sync area riset relationship
        $polaJudul->areaRisets()->sync($this->selectedAreaRiset);
        
        session()->flash('message', $this->editing ? 'Pola Judul berhasil diperbarui.' : 'Pola Judul berhasil dibuat.');
        $this->closeModal();
        $this->resetPage();
    }

    public function confirmDelete(int $polaJudulId): void
    {
        $this->deleteId = $polaJudulId;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            PolaJudul::where('id', $this->deleteId)->delete();
            session()->flash('message', 'Pola Judul berhasil dihapus.');
            $this->cancelDelete();
        }
        $this->resetPage();
    }
};

?>

<div>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pola Judul</h1>
                <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">Kelola template pola judul penelitian</p>
            </div>
            <button wire:click="openCreateModal" 
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors shadow-md hover:shadow-lg">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Pola Judul
                </span>
            </button>
        </div>

        <!-- Flash Message -->
        @if (session()->has('message'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

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
                       placeholder="Cari berdasarkan nama pola atau template..."
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-lg border border-gray-200 dark:border-neutral-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-neutral-900 dark:to-neutral-800">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Nama Pola</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Template</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Area Riset</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-neutral-800 divide-y divide-gray-200 dark:divide-neutral-700">
                        @forelse($this->polaJuduls as $polaJudul)
                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700/50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $polaJudul->deskripsi ?: 'Tanpa nama' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600 dark:text-neutral-400 font-mono bg-gray-50 dark:bg-neutral-700/50 px-3 py-2 rounded-lg max-w-md">
                                        {{ \Illuminate\Support\Str::limit($polaJudul->template_string, 80) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($polaJudul->areaRisets->take(3) as $areaRiset)
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                {{ $areaRiset->kode_area }}
                                            </span>
                                        @endforeach
                                        @if($polaJudul->areaRisets->count() > 3)
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                                +{{ $polaJudul->areaRisets->count() - 3 }}
                                            </span>
                                        @endif
                                        @if($polaJudul->areaRisets->isEmpty())
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                                Tidak ada
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end" x-data="{ open: false }">
                                        <div class="relative">
                                            <button @click="open = !open" 
                                                    class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-neutral-700 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                </svg>
                                            </button>
                                            <div x-show="open" 
                                                 @click.away="open = false"
                                                 x-transition
                                                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-neutral-800 rounded-lg shadow-xl z-10 border border-gray-200 dark:border-neutral-700 overflow-hidden">
                                                <div class="py-1">
                                                    <button wire:click="openEditModal({{ $polaJudul->id }})" 
                                                            @click="open = false"
                                                            class="block w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-neutral-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                                                        <span class="flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                            Edit
                                                        </span>
                                                    </button>
                                                    <button wire:click="confirmDelete({{ $polaJudul->id }})" 
                                                            @click="open = false"
                                                            class="block w-full text-left px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                        <span class="flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                            Hapus
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-neutral-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Tidak ada pola judul ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($this->polaJuduls->hasPages())
                <div class="px-6 py-4 bg-gray-50 dark:bg-neutral-900 border-t border-gray-200 dark:border-neutral-700">
                    {{ $this->polaJuduls->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" 
             x-data="{ show: @entangle('showModal') }" 
             x-show="show" 
             x-cloak
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" 
                     x-show="show"
                     @click="$wire.closeModal()"></div>

                <div class="inline-block align-bottom bg-white dark:bg-neutral-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full relative z-50 max-h-[90vh] overflow-y-auto"
                     x-show="show"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <form wire:submit.prevent="save">
                        <div class="px-6 py-5 bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 border-b border-gray-200 dark:border-neutral-700">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                {{ $editing ? 'Edit Pola Judul' : 'Tambah Pola Judul' }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Lengkapi form di bawah untuk {{ $editing ? 'mengubah' : 'menambahkan' }} pola judul</p>
                        </div>

                        <div class="px-6 py-6 space-y-5">
                            <!-- Nama Pola -->
                            <div>
                                <label for="deskripsi" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-2">Nama Pola</label>
                                <input type="text" 
                                       id="deskripsi"
                                       wire:model="deskripsi"
                                       class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-4 py-3 transition-colors"
                                       placeholder="Pola Implementasi">
                                @error('deskripsi') <span class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                            </div>

                            <!-- Template Editor -->
                            <div x-data="{ 
                                insertVariable(variable) {
                                    const textarea = document.getElementById('templateString');
                                    if (!textarea) return;
                                    
                                    const start = textarea.selectionStart || 0;
                                    const end = textarea.selectionEnd || 0;
                                    const currentValue = textarea.value || '';
                                    const newValue = currentValue.substring(0, start) + variable + currentValue.substring(end);
                                    
                                    // Update textarea value
                                    textarea.value = newValue;
                                    
                                    // Update Livewire model
                                    @this.set('templateString', newValue);
                                    
                                    // Set cursor position after insertion
                                    setTimeout(() => {
                                        textarea.focus();
                                        const newPosition = start + variable.length;
                                        textarea.setSelectionRange(newPosition, newPosition);
                                    }, 50);
                                }
                            }">
                                <label for="templateString" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-2">Template Kalimat</label>
                                <textarea id="templateString"
                                          wire:model="templateString"
                                          rows="4"
                                          class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-4 py-3 transition-colors font-mono text-sm"
                                          placeholder="Contoh: Analisis Perbandingan {metode} dan {algoritma} dalam {studi_kasus}"></textarea>
                                @error('templateString') <span class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                
                                <!-- Variable Helper -->
                                <div class="mt-3">
                                    <label class="block text-xs font-medium text-gray-500 dark:text-neutral-400 mb-2">Variable Helper:</label>
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button"
                                                @click="insertVariable('{metode}')"
                                                class="px-3 py-1.5 text-xs font-medium rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-600 transition-colors border border-gray-300 dark:border-neutral-600">
                                            {metode}
                                        </button>
                                        <button type="button"
                                                @click="insertVariable('{algoritma}')"
                                                class="px-3 py-1.5 text-xs font-medium rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-600 transition-colors border border-gray-300 dark:border-neutral-600">
                                            {algoritma}
                                        </button>
                                        <button type="button"
                                                @click="insertVariable('{studi_kasus}')"
                                                class="px-3 py-1.5 text-xs font-medium rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-600 transition-colors border border-gray-300 dark:border-neutral-600">
                                            {studi_kasus}
                                        </button>
                                        <button type="button"
                                                @click="insertVariable('{objek}')"
                                                class="px-3 py-1.5 text-xs font-medium rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-600 transition-colors border border-gray-300 dark:border-neutral-600">
                                            {objek}
                                        </button>
                                        <button type="button"
                                                @click="insertVariable('{platform}')"
                                                class="px-3 py-1.5 text-xs font-medium rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-600 transition-colors border border-gray-300 dark:border-neutral-600">
                                            {platform}
                                        </button>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500 dark:text-neutral-400">Klik variable untuk menambahkannya ke template di posisi kursor</p>
                                </div>
                            </div>

                            <!-- Area Riset Multi-select -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-3">Area Riset</label>
                                <div class="border border-gray-300 dark:border-neutral-600 rounded-lg p-4 bg-gray-50 dark:bg-neutral-700/50 min-h-[200px] max-h-[300px] overflow-y-auto">
                                    <div class="space-y-2">
                                        @foreach($this->areaRisets as $areaRiset)
                                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white dark:hover:bg-neutral-600 cursor-pointer transition-colors">
                                                <input type="checkbox"
                                                       wire:model="selectedAreaRiset"
                                                       value="{{ $areaRiset->id }}"
                                                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 dark:bg-neutral-700 dark:border-neutral-600">
                                                <div class="flex-1">
                                                    <span class="text-sm font-medium text-gray-700 dark:text-neutral-300">{{ $areaRiset->kode_area }}</span>
                                                    <span class="text-sm text-gray-600 dark:text-neutral-400 ml-2">- {{ $areaRiset->nama_area }}</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    @if($this->areaRisets->isEmpty())
                                        <p class="text-sm text-gray-500 dark:text-neutral-400 text-center py-4">Belum ada area riset. Buat area riset terlebih dahulu.</p>
                                    @endif
                                </div>
                                @error('selectedAreaRiset') <span class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="px-6 py-5 bg-gray-50 dark:bg-neutral-900 border-t border-gray-200 dark:border-neutral-700 flex justify-end gap-3">
                            <button type="button" 
                                    wire:click="closeModal"
                                    class="px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-neutral-300 bg-white dark:bg-neutral-700 border border-gray-300 dark:border-neutral-600 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-600 shadow-sm transition-colors">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all">
                                {{ $editing ? 'Update' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" 
             x-data="{ show: @entangle('showDeleteModal') }" 
             x-show="show" 
             x-cloak
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-25" 
                     x-show="show"
                     @click="$wire.cancelDelete()"></div>

                <div class="inline-block align-bottom bg-white dark:bg-neutral-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-50"
                     x-show="show"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <div class="px-6 py-5">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Konfirmasi Hapus</h3>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-neutral-400 ml-13">Apakah Anda yakin ingin menghapus pola judul ini? Tindakan ini tidak dapat dibatalkan.</p>
                    </div>

                    <div class="px-6 py-5 bg-gray-50 dark:bg-neutral-900 border-t border-gray-200 dark:border-neutral-700 flex justify-end gap-3">
                        <button type="button" 
                                wire:click="cancelDelete"
                                class="px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-neutral-300 bg-white dark:bg-neutral-700 border border-gray-300 dark:border-neutral-600 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-600 shadow-sm transition-colors">
                            Batal
                        </button>
                        <button type="button" 
                                wire:click="delete"
                                class="px-5 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 shadow-md hover:shadow-lg transition-all">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

