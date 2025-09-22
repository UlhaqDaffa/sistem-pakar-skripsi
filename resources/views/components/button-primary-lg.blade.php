<button {{ $attributes->merge(['class' => 'w-full sm:w-auto text-center px-6 py-3 font-semibold rounded-lg bg-primary text-white hover:bg-primary/90 focus:outline-none transition-all duration-200 hover:ring-2 hover:ring-primary/70 dark:hover:ring-offset-neutral-800']) }} >
    {{ $slot }}
</button>
