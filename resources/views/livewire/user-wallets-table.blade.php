<x-filament::section>
    <x-filament::section.heading class="mb-5">
        出入金操作
    </x-filament::section.heading>

    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 ">
        <table class="w-full table-fixed text-sm whitespace-nowrap">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-4 py-2 text-center font-bold text-gray-100 dark:text-gray-300 w-1/4">{{ __('currency.code') }}</th>
                    <th class="px-4 py-2 text-center font-bold text-gray-100 dark:text-gray-300 w-1/4">{{ __('currency.balance') }}</th>
                    <th class="px-4 py-2 text-center font-bold text-gray-100 dark:text-gray-300 w-1/4">{{ __('currency.input_amount') }}</th>
                    <th class="px-4 py-2 text-center font-bold text-gray-100 dark:text-gray-300 w-1/4">{{ __('currency.action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($user->wallets as $wallet)
                @php
                $canDeposit = auth()->user()->can('deposit', $wallet);
                $canWithdraw = auth()->user()->can('withdraw', $wallet);
                @endphp

                <tr class="border-t border-gray-200 dark:border-gray-700">
                    <td class="px-4 py-2 text-center text-gray-900 dark:text-gray-100 w-1/4">
                        {{ $wallet->currencyCode->code }}
                    </td>
                    <td class="px-4 py-2 text-center text-gray-900 dark:text-gray-100 w-1/4">
                        {{ number_format($wallet->balance, 2) }}
                    </td>

                    {{-- ✅ 如果有任一權限就顯示金額輸入框，否則空欄 --}}
                    <td class="px-4 py-2 text-right w-1/4">
                        @if($canDeposit || $canWithdraw)
                        <input
                            type="number"
                            wire:model.defer="amounts.{{ $wallet->id }}"
                            class="w-full filament-input rounded-md border-gray-300 dark:bg-gray-800 dark:text-white" />
                        @endif
                    </td>

                    {{-- ✅ 操作按鈕區 --}}
                    <td class="px-4 py-2 text-center w-1/4">
                        @if($canDeposit)
                        <x-filament::button
                            wire:click="makeDeposit({{ $wallet->id }})"
                            size="sm"
                            color="success"
                            class="mr-2">
                            入金
                        </x-filament::button>
                        @endif

                        @if($canWithdraw)
                        <x-filament::button
                            wire:click="makeWithdraw({{ $wallet->id }})"
                            size="sm"
                            color="danger">
                            出金
                        </x-filament::button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</x-filament::section>