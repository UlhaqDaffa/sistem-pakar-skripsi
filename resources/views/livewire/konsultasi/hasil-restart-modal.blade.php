<section class="space-y-6">
    <flux:modal.trigger name="confirm-user-selection">
        <x-button-ghost-lg>
                Ulangi Konsultasi
        </x-button-ghost-lg>
    </flux:modal.trigger>

    <flux:modal name="confirm-user-selection" focusable class="max-w-lg">
            <div>
                <flux:heading size="lg">{{ __('Ulangi Konsultasi?') }}</flux:heading>

                <flux:subheading class="mb-4">
                    {{ __('Anda akan mengulang konsultasi dengan sistem pakar') }}
                </flux:subheading>
            </div>

            <div class="flex justify-center gap-2 space-x-2">
                <x-button-primary href="{{ route('konsultasi.proses') }}" wire:navigate>
                    Ya
                </x-button-primary>

                <flux:modal.close>
                    <x-button-ghost>
                    Batal
                    </x-button-ghost>
                </flux:modal.close>
            </div>
    </flux:modal>
</section>
