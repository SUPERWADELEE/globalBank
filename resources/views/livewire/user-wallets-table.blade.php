<x-filament::section>
    <x-filament::section.heading class="mb-5">
        出入金操作
    </x-filament::section.heading>

    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 ">
        <table class="w-full table-fixed text-sm whitespace-nowrap">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-4 py-2 text-center font-bold text-gray-100 dark:text-gray-300 w-1/4">貨幣</th>
                    <th class="px-4 py-2 text-center font-bold text-gray-100 dark:text-gray-300 w-1/4">餘額</th>
                    <th class="px-4 py-2 text-center font-bold text-gray-100 dark:text-gray-300 w-1/4">金額</th>
                    <th class="px-4 py-2 text-center font-bold text-gray-100 dark:text-gray-300 w-1/4">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($user->wallets as $wallet)
                <tr class="border-t border-gray-200 dark:border-gray-700">
                    <td class="px-4 py-2 text-center text-gray-900 dark:text-gray-100 w-1/4">{{ $wallet->currencyCode->code }}</td>
                    <td class="px-4 py-2 text-center text-gray-900 dark:text-gray-100 w-1/4">{{ number_format($wallet->balance, 2) }}</td>
                    <td class="px-4 py-2 text-right w-1/4">
                        <input type="number" wire:model.defer="amounts.{{ $wallet->id }}"
                            class="w-full filament-input rounded-md border-gray-300 dark:bg-gray-800 dark:text-white" />
                    </td>
                    <td class="px-4 py-2 text-center w-1/4">
                        <x-filament::button wire:click="makeDeposit({{ $wallet->id }})" size="sm" color="success" class="mr-2">
                            入金
                        </x-filament::button>
                        <x-filament::button wire:click="makeWithdraw({{ $wallet->id }})" size="sm" color="danger">
                            出金
                        </x-filament::button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</x-filament::section>