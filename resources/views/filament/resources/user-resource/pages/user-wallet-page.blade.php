<x-filament::page>
    <h2 class="text-xl font-bold mb-4">{{ $this->user->name }} 的錢包清單</h2>

    @livewire('user-wallets-table', ['user' => $this->user])
</x-filament::page>