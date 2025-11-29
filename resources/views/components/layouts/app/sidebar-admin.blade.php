
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar  class="bg-white/30 dark:bg-zinc-900/30 backdrop-blur-lg border-e border-white/40 dark:border-white/10">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('admin.dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <!-- Dashboard -->
                <flux:navlist.group :heading="__('Dashboard')" class="grid">
                    <flux:navlist.item class="mb-1" icon="home" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                </flux:navlist.group>

                <!-- Master Data -->
                <flux:navlist.group :heading="__('Master Data')" class="grid">
                    <flux:navlist.item class="mb-1" icon="users" :href="route('admin.users.index')" :current="request()->routeIs('admin.users.*')" wire:navigate>{{ __('Users') }}</flux:navlist.item>
                    <flux:navlist.item class="mb-1" icon="heart" :href="route('admin.minat.index')" :current="request()->routeIs('admin.minat.*')" wire:navigate>{{ __('Minat') }}</flux:navlist.item>
                    <flux:navlist.item class="mb-1" icon="map-pin" :href="route('admin.area-riset.index')" :current="request()->routeIs('admin.area-riset.*')" wire:navigate>{{ __('Area Riset') }}</flux:navlist.item>
                    <flux:navlist.item class="mb-1" icon="tag" :href="route('admin.tags.index')" :current="request()->routeIs('admin.tags.*')" wire:navigate>{{ __('Tags') }}</flux:navlist.item>
                </flux:navlist.group>

                <!-- Knowledge Base -->
                <flux:navlist.group :heading="__('Knowledge Base')" class="grid">
                    <flux:navlist.item class="mb-1" icon="question-mark-circle" :href="route('admin.pertanyaan.index')" :current="request()->routeIs('admin.pertanyaan.*')" wire:navigate>{{ __('Pertanyaan') }}</flux:navlist.item>
                    <flux:navlist.item class="mb-1" icon="clipboard-document-list" :href="route('admin.rules.index')" :current="request()->routeIs('admin.rules.*')" wire:navigate>{{ __('Rules') }}</flux:navlist.item>
                    <flux:navlist.item class="mb-1" icon="document-text" :href="route('admin.pola-judul.index')" :current="request()->routeIs('admin.pola-judul.*')" wire:navigate>{{ __('Pola Judul') }}</flux:navlist.item>
                </flux:navlist.group>

                <!-- System -->
                <flux:navlist.group :heading="__('System')" class="grid">
                    <flux:navlist.item class="mb-1" icon="clock" :href="route('admin.riwayat-konsultasi.index')" :current="request()->routeIs('admin.riwayat-konsultasi.*')" wire:navigate>{{ __('Riwayat Konsultasi') }}</flux:navlist.item>
                    <flux:navlist.item class="mb-1" icon="cpu-chip" :href="route('admin.training-model.index')" :current="request()->routeIs('admin.training-model.*')" wire:navigate>{{ __('Training Model') }}</flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>


            <flux:spacer />

            <flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/UlhaqDaffa/sistem-pakar-skripsi" target="_blank">
                {{ __('Repositori Proyek') }}
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                {{ __('Bantuan') }}
                </flux:navlist.item>
            </flux:navlist>

            <!-- Desktop User Menu -->
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('admin.settings.profil')" icon="cog" wire:navigate>
                            {{ __('Pengaturan') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Keluar') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('admin.settings.profil')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
