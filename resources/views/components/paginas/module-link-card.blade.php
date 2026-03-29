@props(['href', 'title', 'description'])

<a
    href="{{ $href }}"
    {{ $attributes->merge(['class' => 'group flex flex-col p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm sm:rounded-lg hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-md transition duration-150 ease-in-out']) }}
>
    <div class="flex items-start gap-4">
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/60">
            {{ $icon }}
        </div>
        <div class="min-w-0 flex-1">
            <h3 class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                {{ $title }}
            </h3>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ $description }}
            </p>
        </div>
        <svg class="h-5 w-5 shrink-0 text-gray-400 group-hover:text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
    </div>
</a>
