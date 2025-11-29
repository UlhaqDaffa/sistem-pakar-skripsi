<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Pertanyaan;
use App\Models\KategoriPertanyaan;
use App\Models\OpsiJawaban;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app-admin')] class extends Component {
    use WithPagination;

    public string $filterKategori = '';
    public bool $showModal = false;
    public bool $editing = false;
    public ?int $id = null;
    public string $teksPertanyaan = '';
    public ?int $kategoriId = null;
    public string $type = 'Pilihan Ganda';
    public array $options = [['label' => '', 'value' => 0]];
    public bool $showDeleteModal = false;
    public ?int $deleteId = null;

    public function getQuestionsProperty()
    {
        $query = Pertanyaan::with('kategori');
        
        if ($this->filterKategori) {
            $query->where('kategori_id', $this->filterKategori);
        }
        
        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getKategorisProperty()
    {
        return KategoriPertanyaan::orderBy('nama_kategori')->get();
    }

    public function openCreateModal(): void
    {
        $this->reset(['id', 'teksPertanyaan', 'kategoriId', 'type', 'options']);
        $this->options = [['label' => '', 'value' => 0]];
        $this->editing = false;
        $this->showModal = true;
    }

    public function openEditModal(int $questionId): void
    {
        $question = Pertanyaan::with('opsiJawaban')->findOrFail($questionId);
        $this->id = $question->id;
        $this->teksPertanyaan = $question->teks_pertanyaan;
        $this->kategoriId = $question->kategori_id;
        
        // Check if question has options
        $opsiJawaban = $question->opsiJawaban;
        if ($opsiJawaban->isNotEmpty()) {
            $this->type = 'Pilihan Ganda';
            $this->options = $opsiJawaban->map(function ($opsi) {
                return [
                    'label' => $opsi->teks_jawaban,
                    'value' => $opsi->nilai,
                ];
            })->toArray();
        } else {
            $this->type = 'Input Nilai';
            $this->options = [['label' => '', 'value' => 0]];
        }
        
        $this->editing = true;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['id', 'teksPertanyaan', 'kategoriId', 'type', 'options', 'editing']);
        $this->options = [['label' => '', 'value' => 0]];
    }

    public function addOption(): void
    {
        $this->options[] = ['label' => '', 'value' => 0];
    }

    public function removeOption(int $index): void
    {
        unset($this->options[$index]);
        $this->options = array_values($this->options);
        
        // Ensure at least one option exists
        if (empty($this->options)) {
            $this->options = [['label' => '', 'value' => 0]];
        }
    }

    public function save(): void
    {
        $rules = [
            'teksPertanyaan' => 'required|string',
            'kategoriId' => 'required|exists:kategori_pertanyaan,id',
            'type' => 'required|in:Pilihan Ganda,Input Nilai',
        ];
        
        if ($this->type === 'Pilihan Ganda') {
            $rules['options'] = 'required|array|min:2';
            $rules['options.*.label'] = 'required|string';
            $rules['options.*.value'] = 'required|numeric';
        }
        
        $this->validate($rules);
        
        // Generate kode_pertanyaan
        $kategori = KategoriPertanyaan::findOrFail($this->kategoriId);
        $kodePrefix = $kategori->kode_kategori;
        
        if ($this->editing) {
            $question = Pertanyaan::findOrFail($this->id);
            $kodePertanyaan = $question->kode_pertanyaan;
        } else {
            // Generate new kode
            $lastQuestion = Pertanyaan::where('kode_pertanyaan', 'like', $kodePrefix . '%')
                ->orderBy('kode_pertanyaan', 'desc')
                ->first();
            
            if ($lastQuestion) {
                $lastNumber = (int) substr($lastQuestion->kode_pertanyaan, strlen($kodePrefix) + 1);
                $kodePertanyaan = $kodePrefix . '_' . str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
            } else {
                $kodePertanyaan = $kodePrefix . '_01';
            }
        }
        
        // Create or update question
        $questionData = [
            'kategori_id' => $this->kategoriId,
            'kode_pertanyaan' => $kodePertanyaan,
            'teks_pertanyaan' => $this->teksPertanyaan,
        ];
        
        if ($this->editing) {
            $question = Pertanyaan::findOrFail($this->id);
            $question->update($questionData);
        } else {
            $question = Pertanyaan::create($questionData);
        }
        
        // Handle options
        if ($this->type === 'Pilihan Ganda') {
            // Delete old options
            OpsiJawaban::where('pertanyaan_id', $question->id)->delete();
            
            // Create new options
            $optionsData = [];
            foreach ($this->options as $index => $option) {
                $optionsData[] = [
                    'pertanyaan_id' => $question->id,
                    'kode_jawaban' => strtoupper($kodePrefix) . '_' . str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                    'teks_jawaban' => $option['label'],
                    'nilai' => $option['value'],
                ];
            }
            OpsiJawaban::insert($optionsData);
        } else {
            // Input Nilai - delete all options
            OpsiJawaban::where('pertanyaan_id', $question->id)->delete();
        }
        
        session()->flash('message', $this->editing ? 'Pertanyaan berhasil diperbarui.' : 'Pertanyaan berhasil dibuat.');
        $this->closeModal();
        $this->resetPage();
    }

    public function confirmDelete(int $questionId): void
    {
        $this->deleteId = $questionId;
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
            Pertanyaan::where('id', $this->deleteId)->delete();
            session()->flash('message', 'Pertanyaan berhasil dihapus.');
            $this->cancelDelete();
        }
        $this->resetPage();
    }

    public function updatingFilterKategori(): void
    {
        $this->resetPage();
    }
};

?>

<div>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Question Bank</h1>
                <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">Kelola pertanyaan dan opsi jawaban</p>
            </div>
            <button wire:click="openCreateModal" 
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Pertanyaan
                </span>
            </button>
        </div>

        <!-- Flash Message -->
        @if (session()->has('message'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        <!-- Filter -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-lg p-5 border border-gray-200 dark:border-neutral-700">
            <div class="flex items-center gap-4">
                <label for="filterKategori" class="text-sm font-semibold text-gray-700 dark:text-neutral-300">Filter Kategori:</label>
                <select id="filterKategori"
                        wire:model.live="filterKategori"
                        class="block w-64 rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-4 py-2.5 transition-colors">
                    <option value="">Semua Kategori</option>
                    @foreach($this->kategoris as $kategori)
                        <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-lg border border-gray-200 dark:border-neutral-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-neutral-900 dark:to-neutral-800">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Kode</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Pertanyaan</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Kategori</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Tipe</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-neutral-800 divide-y divide-gray-200 dark:divide-neutral-700">
                        @forelse($this->questions as $question)
                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700/50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $question->kode_pertanyaan }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-white leading-relaxed">{{ \Illuminate\Support\Str::limit($question->teks_pertanyaan, 80) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1.5 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 shadow-sm">
                                        {{ $question->kategori->nama_kategori }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $hasOptions = $question->opsiJawaban->isNotEmpty();
                                    @endphp
                                    <span class="px-3 py-1.5 inline-flex text-xs font-semibold rounded-full {{ $hasOptions ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300' }} shadow-sm">
                                        {{ $hasOptions ? 'Pilihan Ganda' : 'Input Nilai' }}
                                    </span>
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
                                                    <button wire:click="openEditModal({{ $question->id }})" 
                                                            @click="open = false"
                                                            class="block w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-neutral-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                                                        <span class="flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                            Edit
                                                        </span>
                                                    </button>
                                                    <button wire:click="confirmDelete({{ $question->id }})" 
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
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-neutral-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Tidak ada pertanyaan ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($this->questions->hasPages())
                <div class="px-6 py-4 bg-gray-50 dark:bg-neutral-900 border-t border-gray-200 dark:border-neutral-700">
                    {{ $this->questions->links() }}
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

                <div class="inline-block align-bottom bg-white dark:bg-neutral-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full relative z-50 max-h-[90vh] overflow-y-auto"
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
                                {{ $editing ? 'Edit Pertanyaan' : 'Tambah Pertanyaan' }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Lengkapi form di bawah untuk {{ $editing ? 'mengubah' : 'menambahkan' }} pertanyaan</p>
                        </div>

                        <div class="px-6 py-6 space-y-5">
                            <!-- Question Text -->
                            <div>
                                <label for="teksPertanyaan" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-2">Teks Pertanyaan</label>
                                <textarea id="teksPertanyaan"
                                          wire:model="teksPertanyaan"
                                          rows="4"
                                          class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-4 py-3 transition-colors"
                                          placeholder="Masukkan teks pertanyaan..."></textarea>
                                @error('teksPertanyaan') <span class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                            </div>

                            <!-- Category -->
                            <div>
                                <label for="kategoriId" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-2">Kategori</label>
                                <select id="kategoriId"
                                        wire:model="kategoriId"
                                        class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-4 py-3 transition-colors">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($this->kategoris as $kategori)
                                        <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                    @endforeach
                                </select>
                                @error('kategoriId') <span class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                            </div>

                            <!-- Type -->
                            <div>
                                <label for="type" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-2">Tipe</label>
                                <select id="type"
                                        wire:model.live="type"
                                        class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-4 py-3 transition-colors">
                                    <option value="Pilihan Ganda">Pilihan Ganda</option>
                                    <option value="Input Nilai">Input Nilai</option>
                                </select>
                                @error('type') <span class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                            </div>

                            <!-- Dynamic Options Section -->
                            @if($type === 'Pilihan Ganda')
                                <div class="border-t border-gray-200 dark:border-neutral-700 pt-5">
                                    <div class="flex items-center justify-between mb-4">
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-neutral-300">Opsi Jawaban</label>
                                        <button type="button"
                                                wire:click="addOption"
                                                class="px-4 py-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/20 dark:hover:bg-indigo-900/30 rounded-lg shadow-sm transition-colors flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Tambah Opsi
                                        </button>
                                    </div>

                                    <div class="space-y-3">
                                        @foreach($options as $index => $option)
                                            <div class="flex items-start gap-3 p-4 bg-gray-50 dark:bg-neutral-700/50 rounded-xl border border-gray-200 dark:border-neutral-600 shadow-sm">
                                                <div class="flex-1 space-y-2">
                                                    <label class="block text-xs font-semibold text-gray-600 dark:text-neutral-400 uppercase tracking-wide">Label</label>
                                                    <input type="text"
                                                           wire:model="options.{{ $index }}.label"
                                                           class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white text-sm px-3 py-2 transition-colors"
                                                           placeholder="Teks opsi">
                                                    @error('options.' . $index . '.label') <span class="text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                                </div>
                                                <div class="w-32 space-y-2">
                                                    <label class="block text-xs font-semibold text-gray-600 dark:text-neutral-400 uppercase tracking-wide">Bobot</label>
                                                    <input type="number"
                                                           step="0.1"
                                                           wire:model="options.{{ $index }}.value"
                                                           class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white text-sm px-3 py-2 transition-colors">
                                                    @error('options.' . $index . '.value') <span class="text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                                </div>
                                                <div class="pt-7">
                                                    <button type="button"
                                                            wire:click="removeOption({{ $index }})"
                                                            class="p-2 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('options') <span class="mt-2 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                </div>
                            @endif
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
                        <p class="text-sm text-gray-600 dark:text-neutral-400 ml-13">Apakah Anda yakin ingin menghapus pertanyaan ini? Tindakan ini tidak dapat dibatalkan.</p>
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

