<x-filament::section>

    <x-filament::section.heading class="mb-5">
        出入金紀錄
    </x-filament::section.heading>

    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 ">
        <table class="w-full table-fixed text-sm whitespace-nowrap">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-4 py-2 text-center font-bold text-gray-100 dark:text-gray-300 w-1/4">時間</th>
                    <th class="px-4 py-2 text-center font-bold text-gray-100 dark:text-gray-300 w-1/4">執行項目</th>
                    <th class="px-4 py-2 text-center font-bold text-gray-100 dark:text-gray-300 w-1/4">金額</th>
                    <th class="px-4 py-2 text-center font-bold text-gray-100 dark:text-gray-300 w-1/4">操作帳號/職稱</th>
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
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament::section>