<x-filament::page>
    <form wire:submit.prevent="submit">
        {{ $this->form }}
        <x-filament::button type="submit" class="mt-6">儲存</x-filament::button>
    </form>
</x-filament::page>