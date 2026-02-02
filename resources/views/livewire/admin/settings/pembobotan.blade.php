<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\KonfigurasiPembobotan;
use Livewire\Attributes\Validate;

new #[Layout('components.layouts.app-admin')] class extends Component {
    #[Validate('required|string')]
    public string $nama_konfigurasi = 'default';

    #[Validate('required|numeric|min:0|max:1')]
    public float $bobot_minat = 0.60;

    #[Validate('required|numeric|min:0|max:1')]
    public float $bobot_asesmen = 0.40;

    public ?KonfigurasiPembobotan $konfigurasiAktif = null;

    public function mount(): void
    {
        $this->konfigurasiAktif = KonfigurasiPembobotan::getActive();
        
        if ($this->konfigurasiAktif) {
            $this->nama_konfigurasi = $this->konfigurasiAktif->nama_konfigurasi;
            $this->bobot_minat = (float) $this->konfigurasiAktif->bobot_minat;
            $this->bobot_asesmen = (float) $this->konfigurasiAktif->bobot_asesmen;
        }
    }

    public function updatedBobotMinat(): void
    {
        $this->bobot_asesmen = round(1.0 - $this->bobot_minat, 2);
    }

    public function updatedBobotAsesmen(): void
    {
        $this->bobot_minat = round(1.0 - $this->bobot_asesmen, 2);
    }

    public function simpan(): void
    {
        $this->validate([
            'nama_konfigurasi' => 'required|string',
            'bobot_minat' => 'required|numeric|min:0|max:1',
            'bobot_asesmen' => 'required|numeric|min:0|max:1',
        ], [
            'bobot_minat.required' => 'Bobot minat harus diisi',
            'bobot_minat.numeric' => 'Bobot minat harus berupa angka',
            'bobot_minat.min' => 'Bobot minat minimal 0',
            'bobot_minat.max' => 'Bobot minat maksimal 1',
            'bobot_asesmen.required' => 'Bobot asesmen harus diisi',
            'bobot_asesmen.numeric' => 'Bobot asesmen harus berupa angka',
            'bobot_asesmen.min' => 'Bobot asesmen minimal 0',
            'bobot_asesmen.max' => 'Bobot asesmen maksimal 1',
        ]);

        // Validasi total harus 1.0
        $total = round($this->bobot_minat + $this->bobot_asesmen, 2);
        if ($total != 1.0) {
            $this->addError('bobot_asesmen', 'Total bobot minat dan asesmen harus sama dengan 1.0 (100%)');
            return;
        }

        // Nonaktifkan semua konfigurasi yang aktif
        KonfigurasiPembobotan::where('is_active', true)->update(['is_active' => false]);

        // Update atau create konfigurasi
        if ($this->konfigurasiAktif) {
            $this->konfigurasiAktif->update([
                'nama_konfigurasi' => $this->nama_konfigurasi,
                'bobot_minat' => $this->bobot_minat,
                'bobot_asesmen' => $this->bobot_asesmen,
                'is_active' => true,
            ]);
            session()->flash('status', 'Konfigurasi pembobotan berhasil diperbarui.');
        } else {
            KonfigurasiPembobotan::create([
                'nama_konfigurasi' => $this->nama_konfigurasi,
                'bobot_minat' => $this->bobot_minat,
                'bobot_asesmen' => $this->bobot_asesmen,
                'is_active' => true,
            ]);
            session()->flash('status', 'Konfigurasi pembobotan berhasil dibuat.');
        }

        $this->konfigurasiAktif = KonfigurasiPembobotan::getActive();
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')
    @include('partials.head')

    <x-settings.layout-admin :heading="__('Konfigurasi Pembobotan')" :subheading="__('Atur bobot untuk skor minat dan asesmen')">
        <form wire:submit="simpan" class="my-6 w-full space-y-6">
            <!-- Info Box -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-blue-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-blue-800 dark:text-blue-200">
                        Total bobot minat dan asesmen harus sama dengan <strong>1.0 (100%)</strong>. 
                        Saat Anda mengubah salah satu nilai, nilai lainnya akan otomatis disesuaikan.
                    </p>
                </div>
            </div>

            <!-- Nama Konfigurasi -->
            <div>
                <flux:input
                    wire:model="nama_konfigurasi"
                    label="Nama Konfigurasi"
                    type="text"
                    required
                    placeholder="default"
                />
            </div>

            <!-- Bobot Minat -->
            <div>
                <flux:input
                    wire:model.live="bobot_minat"
                    label="Bobot Minat"
                    type="number"
                    step="0.01"
                    min="0"
                    max="1"
                    required
                    placeholder="0.60"
                />
                <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">
                    Bobot untuk skor minat ({{ number_format($bobot_minat * 100, 0) }}%)
                </p>
                @error('bobot_minat')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bobot Asesmen -->
            <div>
                <flux:input
                    wire:model.live="bobot_asesmen"
                    label="Bobot Asesmen"
                    type="number"
                    step="0.01"
                    min="0"
                    max="1"
                    required
                    placeholder="0.40"
                />
                <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">
                    Bobot untuk skor asesmen ({{ number_format($bobot_asesmen * 100, 0) }}%)
                </p>
                @error('bobot_asesmen')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Total Display -->
            <div class="bg-gray-50 dark:bg-neutral-800/50 border border-gray-200 dark:border-neutral-700 rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700 dark:text-neutral-300">Total Bobot:</span>
                    <span class="text-lg font-bold {{ abs(($bobot_minat + $bobot_asesmen) - 1.0) < 0.01 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ number_format(($bobot_minat + $bobot_asesmen) * 100, 2) }}%
                    </span>
                </div>
                @if(abs(($bobot_minat + $bobot_asesmen) - 1.0) >= 0.01)
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                        Total harus sama dengan 100%
                    </p>
                @endif
            </div>

            <!-- Konfigurasi Aktif Saat Ini -->
            @if($konfigurasiAktif)
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                Konfigurasi Aktif: {{ $konfigurasiAktif->nama_konfigurasi }}
                            </p>
                            <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                                Bobot Minat: {{ number_format($konfigurasiAktif->bobot_minat * 100, 0) }}% | 
                                Bobot Asesmen: {{ number_format($konfigurasiAktif->bobot_asesmen * 100, 0) }}%
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex items-center gap-4 pt-4">
                <flux:button variant="primary" type="submit" class="w-full sm:w-auto">
                    {{ __('Simpan Konfigurasi') }}
                </flux:button>

                <x-action-message class="me-3" on="status">
                    {{ session('status') }}
                </x-action-message>
            </div>
        </form>
    </x-settings.layout-admin>
</section>

