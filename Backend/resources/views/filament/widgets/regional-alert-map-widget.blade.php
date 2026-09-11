<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">{{ __('filament.widgets.regional_map_heading') }}</x-slot>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase border-b dark:text-gray-400">
                    <tr>
                        <th class="py-2 pr-4">{{ __('filament.widgets.table_rank') }}</th>
                        <th class="py-2 pr-4">{{ __('filament.labels.region') }}</th>
                        <th class="py-2 pr-4">{{ __('filament.labels.city') }}</th>
                        <th class="py-2 pr-4">{{ __('filament.labels.governorate') }}</th>
                        <th class="py-2 pr-4">{{ __('filament.widgets.table_reports') }}</th>
                        <th class="py-2">{{ __('filament.widgets.table_last_report') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topRegions as $i => $item)
                    <tr class="border-b last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="py-2 pr-4 text-gray-400">{{ $i + 1 }}</td>
                        <td class="py-2 pr-4 font-semibold">{{ $item->region?->name ?? '—' }}</td>
                        <td class="py-2 pr-4">{{ $item->region?->city?->name ?? '—' }}</td>
                        <td class="py-2 pr-4">{{ $item->region?->city?->governorate?->name ?? '—' }}</td>
                        <td class="py-2 pr-4">
                            <span class="px-2 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-200">
                                {{ $item->reports_count }}
                            </span>
                        </td>
                        <td class="py-2 text-gray-500">
                            {{ $item->last_report ? \Carbon\Carbon::parse($item->last_report)->diffForHumans() : '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-4 text-center text-gray-400">{{ __('filament.widgets.regional_map_empty') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
