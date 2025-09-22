@props(['started'])

<button @click="started = true"
    {{ $attributes->merge(['class' => 'relative mt-8 px-8 py-3 text-lg font-semibold rounded-lg bg-primary text-white overflow-hidden group focus:outline-none transition-all duration-200 focus:ring-2 focus:ring-offset-2 focus:ring-primary/70 dark:focus:ring-offset-neutral-800']) }}
    x-data="{ hovered: false }"
    @mouseenter="hovered = true"
    @mouseleave="hovered = false">
    <span class="absolute inset-0 bg-white transition-all duration-300 ease-out"
            :class="{ 'w-full': hovered, 'w-0': !hovered }">
    </span>
    <span class="relative z-10 transition-colors duration-300 ease-out"
            :class="{ 'text-primary': hovered, 'text-white': !hovered }">
        {{ $slot }}
    </span>
</button>


