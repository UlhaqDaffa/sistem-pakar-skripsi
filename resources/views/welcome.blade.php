<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-g">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Spectra - Research Recommendation</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased text-gray-800 dark:text-gray-200">
    <div class="container mx-auto flex min-h-screen flex-col px-6 lg:px-8">
        <header class="w-full py-6">
            <nav class="flex items-center justify-between">
                <a href="/" class="flex items-center space-x-2">
                    <x-app-logo-icon class="h-8 w-8 text-indigo-600 dark:text-indigo-400" />
                    <div class="flex flex-col">
                        <span class="font-bold text-lg tracking-wider">SPECTRA</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 -mt-1">Research Recommendation</span>
                    </div>
                </a>

                <div class="hidden md:flex items-center space-x-10 mr-2">
                    <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors font-bold">Home</a>
                    <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors font-bold">Features</a>
                    <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors font-bold">How It Works</a>
                    <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors font-bold">About</a>
                </div>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="rounded-xl px-4 py-2 text-sm font-semibold bg-white dark:bg-gray-800/80 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="group inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold bg-white dark:bg-gray-800/80 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Get Started
                            <svg class="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endauth
                @endif
            </nav>
        </header>

        <main class="flex flex-1 flex-col items-center justify-center text-center pb-48">
            <h1 class="text-5xl md:text-7xl font-bold leading-tight">
                <span class="bg-gradient-to-r from-indigo-500 to-purple-500 bg-clip-text text-transparent">Research</span><br>Made Easy
            </h1>

            <p class="mt-6 max-w-2xl text-lg text-gray-600 dark:text-gray-300 pb-8">
                Dengan <span class="font-semibold text-indigo-500">Spectra</span>, kamu dapat dengan mudah mengeksplorasi minat, bakat, dan kemampuan akademikmu. Temukan inspirasi topik penelitian yang sesuai dengan <span class="font-semibold">passion</span>, hingga melahirkan <span class="font-semibold">karya</span> yang bermanfaat tidak hanya untukmu, tetapi juga untuk dunia.
            </p>

            <a href="{{ route('register') }}" class="group mt-8 inline-flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-transform hover:scale-105">
                Mulai Sekarang
                <svg class="ml-2 h-5 w-5 transition-transform group-hover:translate-x-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
                </svg>
            </a>
        </main>

        <footer class="w-full py-6 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} Spectra. All rights reserved.
            </p>
        </footer>
    </div>
</body>

</html>
