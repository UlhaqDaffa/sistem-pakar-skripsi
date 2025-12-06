<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\AreaRiset;
use App\Models\MinatBidang;
use App\Models\Tag;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app-admin')] class extends Component {
    use WithPagination;

    public $search = '';
    public $filterMinat = null;
    public $filterTag = null;
    public bool $showModal = false;
    public bool $editing = false;
    public ?int $id = null;
    public string $kodeArea = '';
    public string $namaArea = '';
    public string $deskripsi = '';
    public string $contohStudiKasus = '';
    public string $tujuanMasalah = '';
    public string $tipeSistem = '';
    public string $targetArketipe = 'GENERAL';
    public int $levelKesulitan = 2;
    public array $selectedMinatBidang = [];
    public array $selectedTags = [];
    public $newTagName = '';
    public $newTagType = 'TEKNOLOGI';
    public bool $showDeleteModal = false;
    public ?int $deleteId = null;

    public function getAreaRisetsProperty()
    {
        $query = AreaRiset::with(['minatBidangs', 'tags']);

        if ($this->search !== '') {
            $search = '%' . $this->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('kode_area', 'like', $search)
                  ->orWhere('nama_area', 'like', $search)
                  ->orWhereHas('minatBidangs', function ($sub) use ($search) {
                      $sub->where('nama_bidang', 'like', $search)
                          ->orWhere('kode_bidang', 'like', $search);
                  })
                  ->orWhereHas('tags', function ($sub) use ($search) {
                      $sub->where('nama_tag', 'like', $search);
                  });
            });
        }

        if ($this->filterMinat) {
            $filterMinat = $this->filterMinat;
            $query->whereHas('minatBidangs', function ($q) use ($filterMinat) {
                $q->where('minat_bidang.id', $filterMinat);
            });
        }

        if ($this->filterTag) {
            $filterTag = $this->filterTag;
            $query->whereHas('tags', function ($q) use ($filterTag) {
                $q->where('tags.id', $filterTag);
            });
        }

        return $query
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getMinatBidangsProperty()
    {
        return MinatBidang::orderBy('nama_bidang')->get();
    }

    public function getTagsProperty()
    {
        return Tag::orderBy('nama_tag')->get();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterMinat(): void
    {
        $this->resetPage();
    }

    public function updatingFilterTag(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset(['id', 'kodeArea', 'namaArea', 'deskripsi', 'contohStudiKasus', 'tujuanMasalah', 'tipeSistem', 'targetArketipe', 'levelKesulitan', 'selectedMinatBidang', 'selectedTags']);
        $this->targetArketipe = 'GENERAL';
        $this->levelKesulitan = 2;
        $this->selectedMinatBidang = [];
        $this->selectedTags = [];
        $this->editing = false;
        $this->showModal = true;
    }

    public function openEditModal(int $areaRisetId): void
    {
        $areaRiset = AreaRiset::with(['minatBidangs', 'tags'])->findOrFail($areaRisetId);
        $this->id = $areaRiset->id;
        $this->kodeArea = $areaRiset->kode_area;
        $this->namaArea = $areaRiset->nama_area;
        $this->deskripsi = $areaRiset->deskripsi;
        $this->contohStudiKasus = $areaRiset->contoh_studi_kasus ?? '';
        $this->tujuanMasalah = $areaRiset->tujuan_masalah ?? '';
        $this->tipeSistem = $areaRiset->tipe_sistem ?? '';
        $this->targetArketipe = $areaRiset->target_arketipe;
        $this->levelKesulitan = $areaRiset->level_kesulitan;
        $this->selectedMinatBidang = $areaRiset->minatBidangs->pluck('id')->toArray();
        $this->selectedTags = $areaRiset->tags->pluck('id')->toArray();
        $this->editing = true;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['id', 'kodeArea', 'namaArea', 'deskripsi', 'contohStudiKasus', 'tujuanMasalah', 'tipeSistem', 'targetArketipe', 'levelKesulitan', 'selectedMinatBidang', 'selectedTags', 'editing']);
        $this->targetArketipe = 'GENERAL';
        $this->levelKesulitan = 2;
        $this->selectedMinatBidang = [];
        $this->selectedTags = [];
    }

    public function toggleTag(int $tagId): void
    {
        if (in_array($tagId, $this->selectedTags)) {
            $this->selectedTags = array_values(array_diff($this->selectedTags, [$tagId]));
        } else {
            $this->selectedTags[] = $tagId;
        }
    }

    public function createTagInline(): void
    {
        $this->validate([
            'newTagName' => 'required|string|max:255',
            'newTagType' => 'required|in:TEKNOLOGI,METODE',
        ]);

        $tag = Tag::firstOrCreate(
            [
                'nama_tag' => $this->newTagName,
                'tipe' => $this->newTagType,
            ]
        );

        if (!in_array($tag->id, $this->selectedTags, true)) {
            $this->selectedTags[] = $tag->id;
        }

        // Reset input agar siap menambah lagi
        $this->newTagName = '';
        $this->newTagType = 'TEKNOLOGI';
    }

    public function save(): void
    {
        $rules = [
            'kodeArea' => [
                'required',
                'string',
                'max:255',
                Rule::unique('area_riset', 'kode_area')->ignore($this->id),
            ],
            'namaArea' => [
                'required',
                'string',
                'max:255',
                Rule::unique('area_riset', 'nama_area')->ignore($this->id),
            ],
            'deskripsi' => 'required|string',
            'contohStudiKasus' => 'nullable|string',
            'tujuanMasalah' => 'nullable|string',
            'tipeSistem' => 'nullable|string',
            'targetArketipe' => 'required|in:CREATOR,ANALIS,ARCHITECT,GENERAL',
            'levelKesulitan' => 'required|integer|min:1|max:3',
            'selectedMinatBidang' => 'required|array|min:1',
            'selectedMinatBidang.*' => 'exists:minat_bidang,id',
        ];
        
        $this->validate($rules);
        
        $data = [
            'kode_area' => $this->kodeArea,
            'nama_area' => $this->namaArea,
            'deskripsi' => $this->deskripsi,
            'contoh_studi_kasus' => $this->contohStudiKasus ?: null,
            'tujuan_masalah' => $this->tujuanMasalah ?: null,
            'tipe_sistem' => $this->tipeSistem ?: null,
            'target_arketipe' => $this->targetArketipe,
            'level_kesulitan' => $this->levelKesulitan,
        ];
        
        if ($this->editing) {
            $areaRiset = AreaRiset::findOrFail($this->id);
            $areaRiset->update($data);
        } else {
            $areaRiset = AreaRiset::create($data);
        }
        
        // Sync relations
        $areaRiset->minatBidangs()->sync($this->selectedMinatBidang);
        $areaRiset->tags()->sync($this->selectedTags);
        
        session()->flash('message', $this->editing ? 'Area Riset berhasil diperbarui.' : 'Area Riset berhasil dibuat.');
        $this->closeModal();
        $this->resetPage();
    }

    public function confirmDelete(int $areaRisetId): void
    {
        $this->deleteId = $areaRisetId;
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
            AreaRiset::where('id', $this->deleteId)->delete();
            session()->flash('message', 'Area Riset berhasil dihapus.');
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
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Area Riset</h1>
                <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">Kelola area riset penelitian</p>
            </div>
            <button wire:click="openCreateModal" 
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors shadow-md hover:shadow-lg">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Area Riset
                </span>
            </button>
        </div>

        <!-- Search & Filters -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-lg p-4 border border-gray-200 dark:border-neutral-700">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <!-- Search -->
                <div class="col-span-1 md:col-span-1">
                    <label class="sr-only" for="searchArea">Cari Area</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                            </svg>
                        </span>
                        <input id="searchArea"
                               type="text"
                               wire:model.live.debounce.300ms="search"
                               placeholder="Cari berdasarkan kode, nama area, minat bidang, atau tag..."
                               class="block w-full pl-10 pr-4 py-2.5 rounded-lg border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-sm text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-colors">
                    </div>
                </div>

                <!-- Filter Minat Bidang -->
                <div class="col-span-1">
                    <label class="sr-only" for="filterMinat">Filter Minat Bidang</label>
                    <select id="filterMinat"
                            wire:model.live="filterMinat"
                            class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-sm text-gray-900 dark:text-white px-4 py-2.5 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-colors">
                        <option value="">Semua Minat Bidang</option>
                        @foreach($this->minatBidangs as $minat)
                            <option value="{{ $minat->id }}">{{ $minat->kode_bidang }} - {{ $minat->nama_bidang }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Tag -->
                <div class="col-span-1">
                    <label class="sr-only" for="filterTag">Filter Tag</label>
                    <select id="filterTag"
                            wire:model.live="filterTag"
                            class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-sm text-gray-900 dark:text-white px-4 py-2.5 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-colors">
                        <option value="">Semua Tag</option>
                        @foreach($this->tags as $tag)
                            <option value="{{ $tag->id }}">{{ $tag->nama_tag }} ({{ $tag->tipe }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Flash Message -->
        @if (session()->has('message'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        <!-- Data Table -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-lg border border-gray-200 dark:border-neutral-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-neutral-900 dark:to-neutral-800">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Kode</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Nama Area</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Deskripsi</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Minat Bidang</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Tags</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-neutral-800 divide-y divide-gray-200 dark:divide-neutral-700">
                        @forelse($this->areaRisets as $areaRiset)
                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700/50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $areaRiset->kode_area }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $areaRiset->nama_area }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600 dark:text-neutral-400 leading-relaxed">{{ \Illuminate\Support\Str::limit($areaRiset->deskripsi, 60) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($areaRiset->minatBidangs->take(2) as $minat)
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                {{ $minat->nama_bidang }}
                                            </span>
                                        @endforeach
                                        @if($areaRiset->minatBidangs->count() > 2)
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                                +{{ $areaRiset->minatBidangs->count() - 2 }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($areaRiset->tags->take(3) as $tag)
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                                {{ $tag->nama_tag }}
                                            </span>
                                        @endforeach
                                        @if($areaRiset->tags->count() > 3)
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                                +{{ $areaRiset->tags->count() - 3 }}
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
                                                    <button wire:click="openEditModal({{ $areaRiset->id }})" 
                                                            @click="open = false"
                                                            class="block w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-neutral-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                                                        <span class="flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                            Edit
                                                        </span>
                                                    </button>
                                                    <button wire:click="confirmDelete({{ $areaRiset->id }})" 
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
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-neutral-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Tidak ada area riset ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($this->areaRisets->hasPages())
                <div class="px-6 py-4 bg-gray-50 dark:bg-neutral-900 border-t border-gray-200 dark:border-neutral-700">
                    {{ $this->areaRisets->links() }}
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
                                {{ $editing ? 'Edit Area Riset' : 'Tambah Area Riset' }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Lengkapi form di bawah untuk {{ $editing ? 'mengubah' : 'menambahkan' }} area riset</p>
                        </div>

                        <div class="px-6 py-6 space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Kode Area -->
                                <div>
                                    <label for="kodeArea" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-2">Kode Area</label>
                                    <input type="text" 
                                           id="kodeArea"
                                           wire:model="kodeArea"
                                           class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-4 py-3 transition-colors"
                                           placeholder="AREA_01">
                                    @error('kodeArea') <span class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                </div>

                                <!-- Nama Area -->
                                <div>
                                    <label for="namaArea" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-2">Nama Area</label>
                                    <input type="text" 
                                           id="namaArea"
                                           wire:model="namaArea"
                                           class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-4 py-3 transition-colors"
                                           placeholder="Nama area riset">
                                    @error('namaArea') <span class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label for="deskripsi" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-2">Deskripsi</label>
                                <textarea id="deskripsi"
                                          wire:model="deskripsi"
                                          rows="3"
                                          class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-4 py-3 transition-colors"
                                          placeholder="Deskripsi area riset"></textarea>
                                @error('deskripsi') <span class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Target Arketipe -->
                                <div>
                                    <label for="targetArketipe" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-2">Target Arketipe</label>
                                    <select id="targetArketipe"
                                            wire:model="targetArketipe"
                                            class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-4 py-3 transition-colors">
                                        <option value="GENERAL">General</option>
                                        <option value="CREATOR">Creator</option>
                                        <option value="ANALIS">Analis</option>
                                        <option value="ARCHITECT">Architect</option>
                                    </select>
                                    @error('targetArketipe') <span class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                </div>

                                <!-- Level Kesulitan -->
                                <div>
                                    <label for="levelKesulitan" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-2">Level Kesulitan</label>
                                    <select id="levelKesulitan"
                                            wire:model="levelKesulitan"
                                            class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-4 py-3 transition-colors">
                                        <option value="1">Low</option>
                                        <option value="2">Medium</option>
                                        <option value="3">High</option>
                                    </select>
                                    @error('levelKesulitan') <span class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Minat Bidang -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-2">Minat Bidang</label>
                                <div class="border border-gray-300 dark:border-neutral-600 rounded-lg p-4 bg-gray-50 dark:bg-neutral-700/50 min-h-[120px] max-h-[200px] overflow-y-auto">
                                    <div class="space-y-2">
                                        @foreach($this->minatBidangs as $minat)
                                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white dark:hover:bg-neutral-600 cursor-pointer transition-colors">
                                                <input type="checkbox"
                                                       wire:model="selectedMinatBidang"
                                                       value="{{ $minat->id }}"
                                                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 dark:bg-neutral-700 dark:border-neutral-600">
                                                <span class="text-sm text-gray-700 dark:text-neutral-300">{{ $minat->nama_bidang }}</span>
                                                @if($minat->deskripsi)
                                                    <span class="text-xs text-gray-500 dark:text-neutral-400">- {{ \Illuminate\Support\Str::limit($minat->deskripsi, 40) }}</span>
                                                @endif
                                            </label>
                                        @endforeach
                                    </div>
                                    @if($this->minatBidangs->isEmpty())
                                        <p class="text-sm text-gray-500 dark:text-neutral-400 text-center py-4">Belum ada minat bidang. Buat minat bidang terlebih dahulu di halaman Minat.</p>
                                    @endif
                                </div>
                                @error('selectedMinatBidang') <span class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                            </div>

                            <!-- Tags -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-3">Tags</label>
                                <div class="border border-gray-300 dark:border-neutral-600 rounded-lg p-4 bg-gray-50 dark:bg-neutral-700/50 min-h-[120px] space-y-4">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($this->tags as $tag)
                                            <button type="button"
                                                    wire:click="toggleTag({{ $tag->id }})"
                                                    class="px-3 py-1.5 rounded-full text-sm font-medium transition-all {{ in_array($tag->id, $selectedTags) ? 'bg-indigo-600 text-white shadow-md' : 'bg-white dark:bg-neutral-600 text-gray-700 dark:text-neutral-300 border border-gray-300 dark:border-neutral-500 hover:bg-gray-100 dark:hover:bg-neutral-500' }}">
                                                {{ $tag->nama_tag }}
                                                @if(in_array($tag->id, $selectedTags))
                                                    <svg class="w-4 h-4 inline-block ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                    @if($this->tags->isEmpty())
                                        <p class="text-sm text-gray-500 dark:text-neutral-400 text-center py-4">Belum ada tags. Buat tags terlebih dahulu di halaman Tags.</p>
                                    @endif

                                    <!-- Inline new tag -->
                                    <div class="border-t border-gray-200 dark:border-neutral-600 pt-4">
                                        <h4 class="text-xs font-semibold text-gray-600 dark:text-neutral-300 uppercase tracking-wide mb-2">Tambah Tag Baru</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                            <div class="md:col-span-2">
                                                <input type="text"
                                                       wire:model="newTagName"
                                                       placeholder="Nama tag baru..."
                                                       class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-3 py-2 text-sm transition-colors">
                                                @error('newTagName') <span class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <select wire:model="newTagType"
                                                        class="flex-1 rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-3 py-2 text-xs transition-colors">
                                                    <option value="TEKNOLOGI">TEKNOLOGI</option>
                                                    <option value="METODE">METODE</option>
                                                </select>
                                                <button type="button"
                                                        wire:click="createTagInline"
                                                        class="px-3 py-2 text-xs font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition-colors">
                                                    Tambah
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Optional Fields -->
                            <div class="border-t border-gray-200 dark:border-neutral-700 pt-5">
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-4">Informasi Tambahan (Opsional)</h4>
                                
                                <div class="space-y-4">
                                    <!-- Contoh Studi Kasus -->
                                    <div>
                                        <label for="contohStudiKasus" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">Contoh Studi Kasus</label>
                                        <textarea id="contohStudiKasus"
                                                  wire:model="contohStudiKasus"
                                                  rows="2"
                                                  class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-4 py-3 transition-colors"
                                                  placeholder="Contoh studi kasus"></textarea>
                                    </div>

                                    <!-- Tujuan Masalah -->
                                    <div>
                                        <label for="tujuanMasalah" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">Tujuan Masalah</label>
                                        <textarea id="tujuanMasalah"
                                                  wire:model="tujuanMasalah"
                                                  rows="2"
                                                  class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-4 py-3 transition-colors"
                                                  placeholder="Tujuan dan masalah"></textarea>
                                    </div>

                                    <!-- Tipe Sistem -->
                                    <div>
                                        <label for="tipeSistem" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">Tipe Sistem</label>
                                        <input type="text" 
                                               id="tipeSistem"
                                               wire:model="tipeSistem"
                                               class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-4 py-3 transition-colors"
                                               placeholder="Tipe sistem">
                                    </div>
                                </div>
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
                        <p class="text-sm text-gray-600 dark:text-neutral-400 ml-13">Apakah Anda yakin ingin menghapus area riset ini? Tindakan ini tidak dapat dibatalkan.</p>
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

