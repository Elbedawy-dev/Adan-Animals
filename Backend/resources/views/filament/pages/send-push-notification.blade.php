<x-filament-panels::page>
    @unless ($this->isPushConfigured())
        <div
            class="mb-6 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-950 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-100"
            role="status"
        >
            <p class="font-medium">{{ __('filament.push.not_configured_heading') }}</p>
            <p class="mt-1 opacity-90">{{ __('filament.push.not_configured_body') }}</p>
        </div>
    @endunless

    <x-filament-panels::form id="form" wire:submit="send">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>
</x-filament-panels::page>
