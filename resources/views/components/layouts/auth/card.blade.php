<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-neutral-100 antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="bg-muted flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-md flex-col gap-6">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>

                <div class="flex flex-col gap-6">
                    <div class="relative rounded-xl border bg-white dark:bg-white/5 dark:border-stone-800 text-stone-800 shadow-xs ">

                        <div class="absolute left-7 top-8 bottom z-10">
                            <a href="{{ route('home') }}" class="text-stone-500 hover:text-stone-700 dark:text-stone-400 dark:hover:text-stone-200" wire:navigate>
                                <svg class="size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                                </svg>
                            </a>
                        </div>

                        <div class="px-10 py-8">
                            <div class="flex justify-center mb-6">
                                <a href="{{ route('home') }}" wire:navigate>
                                    <x-app-logo-icon class="size-25 fill-current text-black dark:text-white" />
                                </a>
                            </div>

                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
