
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale())}}" class="light">

<head>
    <title>{{ $title ?? 'Spectra - Rekomendasi Topik' }}</title>
    @include('partials.head')
</head>

<body class="antialiased text-gray-800 dark:text-gray-200">
    <div class="container mx-auto flex min-h-screen flex-col px-6 lg:px-8">
        <header class="w-full py-6">
            <nav class="flex items-center justify-between">
                <a href="/" class="flex items-center space-x-2">
                    <x-app-logo-icon class="size-14 h-14 w-14 text-indigo-600 dark:text-indigo-400" />
                    <div class="flex flex-col">
                        <span class="font-bold text-lg tracking-wider">SPECTRA</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 -mt-1">Rekomendasi Topik</span>
                    </div>
                </a>

                <div class="hidden md:flex items-center space-x-10 mr-2 ml-22 pr-24">
                    <a href="{{ route('home') }}" class="transition-colors font-bold {{ request()->routeIs('home') ? 'text-indigo-600 dark:text-indigo-400' : 'hover:text-indigo-600 dark:hover:text-indigo-400' }}">
                        Home
                        @if(request()->routeIs('home'))
                            <span class="block h-0.5 bg-indigo-600 dark:bg-indigo-400 mt-1"></span>
                        @endif
                    </a>
                    <a href="{{ route('feature') }}" class="transition-colors font-bold {{ request()->routeIs('feature') ? 'text-indigo-600 dark:text-indigo-400' : 'hover:text-indigo-600 dark:hover:text-indigo-400' }}">
                        Fitur
                        @if(request()->routeIs('feature'))
                            <span class="block h-0.5 bg-indigo-600 dark:bg-indigo-400 mt-1"></span>
                        @endif
                    </a>
                    <a href="{{ route('privacy') }}" class="transition-colors font-bold {{ request()->routeIs('privacy') ? 'text-indigo-600 dark:text-indigo-400' : 'hover:text-indigo-600 dark:hover:text-indigo-400' }}">
                        Privasi
                        @if(request()->routeIs('privacy'))
                            <span class="block h-0.5 bg-indigo-600 dark:bg-indigo-400 mt-1"></span>
                        @endif
                    </a>
                    <a href="{{ route('about') }}" class="transition-colors font-bold {{ request()->routeIs('about') ? 'text-indigo-600 dark:text-indigo-400' : 'hover:text-indigo-600 dark:hover:text-indigo-400' }}">
                        Tentang
                        @if(request()->routeIs('about'))
                            <span class="block h-0.5 bg-indigo-600 dark:bg-indigo-400 mt-1"></span>
                        @endif
                    </a>
                </div>

                <div class="flex items-center space-x-2">
                    <x-darkmode-toggle />

                    @if (Route::has('login'))
                        <a href="{{ auth()->check() ? url('/dashboard') : route('login') }}" class="group inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold bg-white dark:bg-gray-800/80 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Masuk
                            <svg class="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif
                </div>
            </nav>
        </header>

        {{ $slot }}

        <footer class="w-full py-6 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} Daffa Dhiya Ulhaq.
            </p>
        </footer>
    </div>
    @fluxScripts
</body>

</html>
