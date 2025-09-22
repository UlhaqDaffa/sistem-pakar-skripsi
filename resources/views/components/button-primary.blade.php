<button {{ $attributes->merge(['class' => 'px-8 py-3 text-sm font-semibold rounded-lg bg-primary text-white hover:bg-primary/90 focus:outline-none transition-all duration-200 hover:ring-2 hover:ring-primary/70 dark:hover:ring-offset-neutral-800']) }} >
    {{ $slot }}
</button>
