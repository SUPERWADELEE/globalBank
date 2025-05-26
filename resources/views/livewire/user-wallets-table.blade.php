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

                $canDeposit = Auth::user()->can('deposit', $wallet);
                $canWithdraw = Auth::user()->can('withdraw', $wallet);

                @endphp

                <tr class="border-t border-gray-200 dark:border-gray-700">
                    <td class="px-4 py-2 text-center text-gray-900 dark:text-gray-100 w-1/4">
                        {{ $wallet->currencyCode->code }}
                    </td>
                    <td class="px-4 py-2 text-center text-gray-900 dark:text-gray-100 w-1/4">
                        {{ number_format($wallet->balance, 2) }}
                    </td>

                    <td class="px-4 py-2 text-right w-1/4">
                        @if($canDeposit || $canWithdraw)
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            wire:model.defer="amounts.{{ $wallet->id }}"
                            oninput="
                                const inputValue = this.value;
                                const regex = /^\d*\.?\d{0,2}$/;
                                if (!regex.test(inputValue)) {
                                    this.value = inputValue.slice(0, -1);
                                }
                            "
                            class="w-full filament-input rounded-md border-gray-300 dark:bg-gray-800 dark:text-white" />
                        @endif
                    </td>

                    <td class="px-4 py-2 text-center w-1/4">
                        @if($canDeposit)
                        <x-filament::button
                            wire:click="confirmDeposit({{ $wallet->id }})"
                            size="sm"
                            color="success"
                            class="mr-2"
                            :disabled="$isProcessing">
                            @if($isProcessing)
                                <x-filament::loading-indicator class="h-4 w-4" />
                            @endif
                            入金
                        </x-filament::button>
                        @endif

                        @if($canWithdraw)
                        <x-filament::button
                            wire:click="confirmWithdraw({{ $wallet->id }})"
                            size="sm"
                            color="danger"
                            :disabled="$isProcessing">
                            @if($isProcessing)
                                <x-filament::loading-indicator class="h-4 w-4" />
                            @endif
                            出金
                        </x-filament::button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <x-filament::modal

        id="wallet-confirm-modal"
        icon="heroicon-o-question-mark-circle"
        heading="{{ __('wallet.confirm_operation', [
            'action' => __('wallet.actions.' . $confirmAction)
        ]) }}"
        wire:model="showConfirmation">
        <p class="text-sm text-gray-500 dark:text-gray-300">
            {{ __('wallet.confirm_operation', [
              'action' => __('wallet.actions.' . $confirmAction)
            ]) }}
        </p>
        <x-slot name="footer">
            <x-filament::button color="gray" wire:click="closeModal">
                取消
            </x-filament::button>

            <x-filament::button color="primary" wire:click="executeConfirmedAction">
                確認
            </x-filament::button>
        </x-slot>
    </x-filament::modal>

</x-filament::section>