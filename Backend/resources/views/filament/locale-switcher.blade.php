<div class="flex items-center gap-2 px-2 text-sm text-gray-600 dark:text-gray-300">
    <span class="hidden sm:inline">{{ __('filament.locale_label') }}</span>
    <a
        href="{{ route('admin.set-locale', ['locale' => 'en']) }}"
        class="rounded px-2 py-1 font-medium hover:bg-gray-100 dark:hover:bg-gray-800 {{ app()->getLocale() === 'en' ? 'bg-gray-200 dark:bg-gray-700' : '' }}"
    >{{ __('filament.locale_en') }}</a>
    <a
        href="{{ route('admin.set-locale', ['locale' => 'ar']) }}"
        class="rounded px-2 py-1 font-medium hover:bg-gray-100 dark:hover:bg-gray-800 {{ app()->getLocale() === 'ar' ? 'bg-gray-200 dark:bg-gray-700' : '' }}"
    >{{ __('filament.locale_ar') }}</a>
</div>
