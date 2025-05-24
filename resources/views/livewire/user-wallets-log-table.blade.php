<x-filament::section>

    <x-filament::section.heading class="mb-5">
        出入金紀錄
    </x-filament::section.heading>

    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 ">
        <table class="w-full table-fixed text-sm whitespace-nowrap">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-4 py-2 text-center font-bold text-gray-100 dark:text-gray-300 w-1/4">{{ __('user_wallets_log.time') }}</th>
                    <th class="px-4 py-2 text-center font-bold text-gray-100 dark:text-gray-300 w-1/4">{{ __('user_wallets_log.action') }}</th>
                    <th class="px-4 py-2 text-center font-bold text-gray-100 dark:text-gray-300 w-1/4">{{ __('user_wallets_log.amount') }}</th>
                    <th class="px-4 py-2 text-center font-bold text-gray-100 dark:text-gray-300 w-1/4">{{ __('user_wallets_log.operator') }}</th>
                    <th class="px-4 py-2 text-center font-bold text-gray-100 dark:text-gray-300 w-1/4">{{ __('user_wallets_log.job_title') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($activityLogs as $wallet)
                    @foreach ($wallet->activityLogs ?? [] as $activity_log)
                    <tr class="border-t border-gray-200 dark:border-gray-700">
                        <td class="px-4 py-2 text-center text-gray-900 dark:text-gray-100 w-1/4">{{ $activity_log->created_at }}</td>
                        <td class="px-4 py-2 text-center text-gray-900 dark:text-gray-100 w-1/4">{{ $activity_log->content }}</td>
                        <td class="px-4 py-2 text-center text-gray-900 dark:text-gray-100 w-1/4">
                            {{ $activity_log->balance_change }}
                        </td>
                        <td class="px-4 py-2 text-center w-1/4">
                            {{ $activity_log->causer_name }}
                        </td>
                        <td class="px-4 py-2 text-center w-1/4">
                            {{ $activity_log->causer_job_title }}
                        </td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament::section>