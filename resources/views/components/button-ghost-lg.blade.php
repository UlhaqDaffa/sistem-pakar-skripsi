<button {{ $attributes->merge(['class' => 'w-full sm:w-auto text-center px-6 py-3 font-semibold rounded-lg dark:bg-neutral-700 text-gray-800 dark:text-white hover:bg-gray-300 dark:hover:bg-neutral-600 focus:outline-none transition-all duration-200 hover:ring-2 hover:ring-gray-400 dark:hover:ring-neutral-500 dark:hover:ring-offset-neutral-800']) }} >
    {{ $slot }}
</button>
