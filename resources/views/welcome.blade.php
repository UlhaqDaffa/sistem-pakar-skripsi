<x-layouts.guest>
    <main class="flex flex-1 flex-col items-center justify-center text-center pb-48 pt-16">
        <h1 class="text-5xl md:text-7xl font-bold leading-tight">
            <span class="bg-gradient-to-r from-indigo-500 to-purple-500 bg-clip-text text-transparent">Research</span><br>Made Easy
        </h1>

        <p class="mt-12 max-w-2xl text-lg text-gray-600 dark:text-gray-300 pb-8">
            Dengan <span class="font-semibold text-indigo-500">Spectra</span>, kamu dapat dengan mudah mengeksplorasi minat, bakat, dan kemampuan akademikmu. Temukan inspirasi topik penelitian yang sesuai dengan <span class="font-semibold">passion</span>, hingga melahirkan <span class="font-semibold">karya</span> yang bermanfaat tidak hanya untukmu, tetapi juga untuk dunia.
        </p>

        <a href="{{ route('register') }}" class="group mt-8 inline-flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-transform hover:scale-105">
            Mulai Sekarang
            <svg class="ml-2 h-5 w-5 transition-transform group-hover:translate-x-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
            </svg>
        </a>
    </main>
</x-layouts.guest>