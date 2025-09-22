<section class="mt-2 space-y-6">
    <flux:modal.trigger name="confirm-user-selection">
        <x-consultation-button>
                Mulai Konsultasi
        </x-consultation-button>
    </flux:modal.trigger>

    <flux:modal name="confirm-user-selection" focusable class="max-w-lg">
            <div>
                <flux:heading size="lg">{{ __('Mulai Konsultasi?') }}</flux:heading>

                <flux:subheading class="mb-4">
                    {{ __('Anda akan memulai konsultasi dengan sistem pakar') }}
                </flux:subheading>
            </div>

            <div class="flex justify-center gap-2 space-x-2">
                <x-button-primary href="{{ route('konsultasi.proses') }}" wire:navigate>
                    Mulai
                </x-button-primary>

                <flux:modal.close>
                    <x-button-ghost>
                    Batal
                    </x-button-ghost>
                </flux:modal.close>
            </div>
    </flux:modal>
</section>
