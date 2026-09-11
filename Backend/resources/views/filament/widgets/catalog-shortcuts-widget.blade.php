<x-filament-widgets::widget>
    <x-filament::section
        :heading="__('filament.widgets.catalog.heading')"
        :description="__('filament.widgets.catalog.description')"
    >
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($items as $item)
                <a
                    href="{{ $item['url'] }}"
                    wire:navigate
                    @class([
                        'fi-catalog-shortcut group flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition',
                        'hover:border-primary-300 hover:bg-primary-50/60 dark:border-white/10 dark:bg-white/5 dark:hover:border-primary-600 dark:hover:bg-primary-950/40',
                    ])
                >
                    <span
                        @class([
                            'flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-700',
                            'group-hover:bg-primary-100 group-hover:text-primary-700 dark:bg-white/10 dark:text-gray-200 dark:group-hover:bg-primary-900/50 dark:group-hover:text-primary-300',
                        ])
                    >
                        <x-filament::icon :icon="$item['icon']" class="h-6 w-6" />
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate font-semibold text-gray-950 dark:text-white">
                            {{ $item['label'] }}
                        </span>
                    </span>
                    <x-filament::icon
                        icon="heroicon-m-chevron-right"
                        class="h-5 w-5 shrink-0 text-gray-400 group-hover:text-primary-600 dark:text-gray-500 dark:group-hover:text-primary-400 rtl:rotate-180"
                    />
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
