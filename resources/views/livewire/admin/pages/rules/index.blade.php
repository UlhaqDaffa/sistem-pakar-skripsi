<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Rule;
use App\Models\AreaRiset;
use Livewire\Attributes\Layout;
use Illuminate\Validation\Rule as ValidationRule;

new #[Layout('components.layouts.app-admin')] class extends Component {
    use WithPagination;

    public bool $showModal = false;
    public bool $editing = false;
    public ?int $id = null;
    public string $kodeRule = '';
    public string $namaRule = '';
    public string $deskripsi = '';
    public ?int $areaRisetId = null;
    public int $prioritas = 100;
    public bool $isActive = true;

    // Fields untuk Rule-Based System (RBS)
    public int $minScore = 0;
    public int $maxScore = 100;
    public int $minSkillLevel = 1;
    public array $allowedArchetypes = [];

    // Engine type & konfigurasi DT opsional
    public string $engineType = 'rule_based';
    public array $dtConfig = [];

    public bool $showDeleteModal = false;
    public ?int $deleteId = null;
    public string $engineFilter = 'all';
    public string $search = '';

    public function getRulesProperty()
    {
        $query = Rule::with(['areaRiset'])
            ->orderBy('prioritas')
            ->orderBy('created_at', 'desc');

        if ($this->engineFilter === 'decision_tree') {
            $query->where('engine_type', 'decision_tree');
        } elseif ($this->engineFilter === 'rule_based') {
            $query->where('engine_type', 'rule_based');
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('kode_rule', 'like', '%' . $this->search . '%')
                  ->orWhere('nama_rule', 'like', '%' . $this->search . '%');
            });
        }

        return $query->paginate(10);
    }

    public function getAreaRisetsProperty()
    {
        return AreaRiset::orderBy('nama_area')->get();
    }

    public function openCreateModal(): void
    {
        $this->reset([
            'id',
            'kodeRule',
            'namaRule',
            'deskripsi',
            'areaRisetId',
            'prioritas',
            'isActive',
            'minScore',
            'maxScore',
            'minSkillLevel',
            'allowedArchetypes',
            'engineType',
            'dtConfig',
        ]);
        $this->prioritas = 100;
        $this->isActive = true;
        $this->engineType = 'rule_based';
        $this->minScore = 0;
        $this->maxScore = 100;
        $this->minSkillLevel = 1;
        $this->allowedArchetypes = [];
        $this->editing = false;
        $this->showModal = true;
    }

    public function openEditModal(int $ruleId): void
    {
        $rule = Rule::findOrFail($ruleId);
        $this->id = $rule->id;
        $this->kodeRule = $rule->kode_rule;
        $this->namaRule = $rule->nama_rule;
        $this->deskripsi = $rule->deskripsi ?? '';
        $this->areaRisetId = $rule->area_riset_id;
        $this->prioritas = $rule->prioritas;
        $this->isActive = $rule->is_active;
        $this->minScore = $rule->min_score ?? 0;
        $this->maxScore = $rule->max_score ?? 100;
        $this->minSkillLevel = $rule->min_skill_level ?? 1;
        $this->allowedArchetypes = $rule->allowed_archetypes ?? [];
        $this->engineType = $rule->engine_type ?? 'rule_based';
        $this->dtConfig = $rule->dt_config ?? [];

        $this->editing = true;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset([
            'id',
            'kodeRule',
            'namaRule',
            'deskripsi',
            'areaRisetId',
            'prioritas',
            'isActive',
            'minScore',
            'maxScore',
            'minSkillLevel',
            'allowedArchetypes',
            'engineType',
            'dtConfig',
            'editing',
        ]);
        $this->prioritas = 100;
        $this->isActive = true;
    }

    public function setEngineFilter(string $filter): void
    {
        if (!in_array($filter, ['all', 'rule_based', 'decision_tree'], true)) {
            return;
        }

        $this->engineFilter = $filter;
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function save(): void
    {
        $rules = [
            'kodeRule' => [
                'required',
                'string',
                'max:255',
                ValidationRule::unique('rules', 'kode_rule')->ignore($this->id),
            ],
            'namaRule' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'areaRisetId' => 'required|exists:area_riset,id',
            'prioritas' => 'required|integer|min:1',
            'isActive' => 'boolean',
            'engineType' => 'required|in:rule_based,decision_tree',
            'minScore' => 'required|integer|min:0',
            'maxScore' => 'required|integer|gte:minScore',
            'minSkillLevel' => 'required|integer|min:1|max:4',
            'allowedArchetypes' => 'array',
            'allowedArchetypes.*' => 'in:CREATOR,ANALIS,ARCHITECT,GENERAL',
        ];
        
        $this->validate($rules);

        // Build aksi JSON
        $aksi = [
            'area_riset_id' => $this->areaRisetId,
            'skor_boost' => 10, // Default score boost
        ];
        
        $data = [
            'kode_rule' => $this->kodeRule,
            'nama_rule' => $this->namaRule,
            'deskripsi' => $this->deskripsi ?: null,
            'aksi' => $aksi,
            'min_score' => $this->minScore,
            'max_score' => $this->maxScore,
            'min_skill_level' => $this->minSkillLevel,
            'allowed_archetypes' => $this->engineType === 'rule_based' ? $this->allowedArchetypes : null,
            'engine_type' => $this->engineType,
            'dt_config' => $this->engineType === 'decision_tree' ? $this->dtConfig : null,
            'area_riset_id' => $this->areaRisetId,
            'prioritas' => $this->prioritas,
            'is_active' => $this->isActive,
        ];
        
        if ($this->editing) {
            Rule::where('id', $this->id)->update($data);
            session()->flash('message', 'Rule berhasil diperbarui.');
        } else {
            Rule::create($data);
            session()->flash('message', 'Rule berhasil dibuat.');
        }
        
        $this->closeModal();
        $this->resetPage();
    }

    public function confirmDelete(int $ruleId): void
    {
        $this->deleteId = $ruleId;
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
            Rule::where('id', $this->deleteId)->delete();
            session()->flash('message', 'Rule berhasil dihapus.');
            $this->cancelDelete();
        }
        $this->resetPage();
    }

    public function formatRuleSummary(Rule $rule): string
    {
        if ($rule->engine_type === 'decision_tree') {
            return 'Decision Tree mapping';
        }

        $range = "Skor {$rule->min_score}–{$rule->max_score}";
        $skill = "Skill ≥ {$rule->min_skill_level}";
        $archetypes = $rule->allowed_archetypes
            ? implode(', ', $rule->allowed_archetypes)
            : 'Semua arketipe';

        return "{$range}; {$skill}; {$archetypes}";
    }
};

?>

<div>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Rules</h1>
                <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">Kelola rules untuk sistem pakar</p>
            </div>
            <button wire:click="openCreateModal" 
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors shadow-md hover:shadow-lg">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Rule
                </span>
            </button>
        </div>

        <!-- Filter Engine -->
        <div class="flex flex-wrap items-center gap-3">
            @php
                $filters = [
                    'all' => 'Semua Rules',
                    'rule_based' => 'Rule-Based',
                    'decision_tree' => 'Decision Tree',
                ];
            @endphp
            @foreach($filters as $value => $label)
                <button
                    wire:click="setEngineFilter('{{ $value }}')"
                    @class([
                        'px-4 py-2 text-sm font-semibold rounded-full transition-colors shadow-sm',
                        'bg-primary text-white' => $engineFilter === $value,
                        'bg-gray-100 dark:bg-neutral-800 text-gray-600 dark:text-neutral-300 hover:bg-gray-200 dark:hover:bg-neutral-700' => $engineFilter !== $value,
                    ])
                >
                    {{ $label }}
                </button>
            @endforeach
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
                       placeholder="Cari berdasarkan kode rule atau nama rule..."
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-lg border border-gray-200 dark:border-neutral-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-neutral-900 dark:to-neutral-800">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Kode Rule</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Nama Rule</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Ringkasan Rule</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Area Riset</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Prioritas</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-700 dark:text-neutral-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-neutral-800 divide-y divide-gray-200 dark:divide-neutral-700">
                        @forelse($this->rules as $rule)
                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700/50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $rule->kode_rule }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $rule->nama_rule }}</div>
                                    @if($rule->deskripsi)
                                        <div class="text-xs text-gray-500 dark:text-neutral-400 mt-1">{{ \Illuminate\Support\Str::limit($rule->deskripsi, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-700 dark:text-neutral-300 font-mono bg-gray-50 dark:bg-neutral-700/50 px-3 py-2 rounded-lg">
                                        {{ $this->formatRuleSummary($rule) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600 dark:text-neutral-400">
                                        {{ $rule->areaRiset?->nama_area ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                        {{ $rule->prioritas }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $rule->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }} shadow-sm">
                                        {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
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
                                                    <button wire:click="openEditModal({{ $rule->id }})" 
                                                            @click="open = false"
                                                            class="block w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-neutral-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                                                        <span class="flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                            Edit
                                                        </span>
                                                    </button>
                                                    <button wire:click="confirmDelete({{ $rule->id }})" 
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
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-neutral-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Tidak ada rule ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($this->rules->hasPages())
                <div class="px-6 py-4 bg-gray-50 dark:bg-neutral-900 border-t border-gray-200 dark:border-neutral-700">
                    {{ $this->rules->links() }}
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

                <div class="inline-block align-bottom bg-white dark:bg-neutral-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full relative z-50 max-h-[90vh] overflow-y-auto"
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
                                {{ $editing ? 'Edit Rule' : 'Tambah Rule' }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Lengkapi form di bawah untuk {{ $editing ? 'mengubah' : 'menambahkan' }} rule</p>
                        </div>

                        <div class="px-6 py-6 space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Kode Rule -->
                                <div>
                                    <label for="kodeRule" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-2">Kode Rule</label>
                                    <input type="text" 
                                           id="kodeRule"
                                           wire:model="kodeRule"
                                           class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-4 py-3 transition-colors"
                                           placeholder="RULE_01">
                                    @error('kodeRule') <span class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                </div>

                                <!-- Nama Rule -->
                                <div>
                                    <label for="namaRule" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-2">Nama Rule</label>
                                    <input type="text" 
                                           id="namaRule"
                                           wire:model="namaRule"
                                           class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-4 py-3 transition-colors"
                                           placeholder="Nama rule">
                                    @error('namaRule') <span class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label for="deskripsi" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-2">Deskripsi</label>
                                <textarea id="deskripsi"
                                          wire:model="deskripsi"
                                          rows="2"
                                          class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-4 py-3 transition-colors"
                                          placeholder="Deskripsi rule (opsional)"></textarea>
                                @error('deskripsi') <span class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Area Riset -->
                                <div>
                                    <label for="areaRisetId" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-2">Area Riset</label>
                                    <select id="areaRisetId"
                                            wire:model="areaRisetId"
                                            class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-4 py-3 transition-colors">
                                        <option value="">Pilih Area Riset</option>
                                        @foreach($this->areaRisets as $areaRiset)
                                            <option value="{{ $areaRiset->id }}">{{ $areaRiset->kode_area }} - {{ $areaRiset->nama_area }}</option>
                                        @endforeach
                                    </select>
                                    @error('areaRisetId') <span class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                </div>

                                <!-- Prioritas -->
                                <div>
                                    <label for="prioritas" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-2">Prioritas</label>
                                    <input type="number" 
                                           id="prioritas"
                                           wire:model="prioritas"
                                           min="1"
                                           class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-4 py-3 transition-colors"
                                           placeholder="100">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">Semakin kecil nilai, semakin tinggi prioritas</p>
                                    @error('prioritas') <span class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Status Aktif -->
                            <div>
                                <label class="flex items-center gap-3">
                                    <input type="checkbox"
                                           wire:model="isActive"
                                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 dark:bg-neutral-700 dark:border-neutral-600">
                                    <span class="text-sm font-semibold text-gray-700 dark:text-neutral-300">Rule Aktif</span>
                                </label>
                            </div>

                            <!-- Engine & RBS Fields -->
                            <div class="border-t border-gray-200 dark:border-neutral-700 pt-5 space-y-4">
                                <!-- Engine Type -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-2">Engine</label>
                                    <select wire:model="engineType"
                                            class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-4 py-3 transition-colors">
                                        <option value="rule_based">Rule-Based</option>
                                        <option value="decision_tree">Decision Tree</option>
                                    </select>
                                    @error('engineType') <span class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                </div>

                                <!-- RBS-only fields -->
                                <div x-data x-show="$wire.engineType === 'rule_based'" x-cloak class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <!-- Min Score -->
                                        <div>
                                            <label for="minScore" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-2">Min Score</label>
                                            <input type="number"
                                                   id="minScore"
                                                   wire:model="minScore"
                                                   class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-4 py-3 transition-colors">
                                            @error('minScore') <span class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                        </div>

                                        <!-- Max Score -->
                                        <div>
                                            <label for="maxScore" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-2">Max Score</label>
                                            <input type="number"
                                                   id="maxScore"
                                                   wire:model="maxScore"
                                                   class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-4 py-3 transition-colors">
                                            @error('maxScore') <span class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Min Skill Level -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div>
                                            <label for="minSkillLevel" class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-2">Min Skill Level</label>
                                            <select id="minSkillLevel"
                                                    wire:model="minSkillLevel"
                                                    class="block w-full rounded-lg border-gray-300 dark:border-neutral-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-white px-4 py-3 transition-colors">
                                                <option value="1">1 - Sangat Dasar</option>
                                                <option value="2">2 - Dasar</option>
                                                <option value="3">3 - Menengah</option>
                                                <option value="4">4 - Lanjut</option>
                                            </select>
                                            @error('minSkillLevel') <span class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                        </div>

                                        <!-- Allowed Archetypes -->
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-neutral-300 mb-2">Allowed Archetypes</label>
                                            <div class="grid grid-cols-2 gap-2">
                                                @foreach(['CREATOR','ANALIS','ARCHITECT','GENERAL'] as $arch)
                                                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-neutral-300">
                                                        <input type="checkbox"
                                                               value="{{ $arch }}"
                                                               wire:model="allowedArchetypes"
                                                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 dark:bg-neutral-700 dark:border-neutral-600">
                                                        <span>{{ $arch }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                            @error('allowedArchetypes') <span class="mt-1 text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Placeholder untuk konfigurasi Decision Tree (opsional) -->
                                <div x-data x-show="$wire.engineType === 'decision_tree'" x-cloak>
                                    <p class="text-xs text-gray-500 dark:text-neutral-400">
                                        Konfigurasi khusus Decision Tree dapat ditambahkan di sini (dt_config).
                                    </p>
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
                        <p class="text-sm text-gray-600 dark:text-neutral-400 ml-13">Apakah Anda yakin ingin menghapus rule ini? Tindakan ini tidak dapat dibatalkan.</p>
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




