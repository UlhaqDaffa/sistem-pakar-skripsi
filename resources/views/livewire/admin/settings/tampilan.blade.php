<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[layout('components.layouts.app-admin')] class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout-admin :heading="__('Tampilan Web')" :subheading=" __('Perbarui tampilan web Anda')">
        <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
            <flux:radio value="light" icon="sun">{{ __('Terang') }}</flux:radio>
            <flux:radio value="dark" icon="moon">{{ __('Gelap') }}</flux:radio>
            <flux:radio value="system" icon="computer-desktop">{{ __('Sistem') }}</flux:radio>
        </flux:radio.group>
    </x-settings.layout-admin>
</section>
