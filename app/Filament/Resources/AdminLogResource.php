<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminLogResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use App\Enums\DepositLocationStatus;
use App\Models\CustomActivity;
use Illuminate\Database\Eloquent\Builder;

class AdminLogResource extends Resource
{
    protected static ?string $model = CustomActivity::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('system_management.title');
    }
    public static function getNavigationLabel(): string
    {
        return __('admin_user.activity_log.title');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('causer.name')->label(__('admin_user.activity_log.causer')),
                TextColumn::make('description')
                    ->label(__('admin_user.activity_log.description'))
                    ->formatStateUsing(fn($state, $record) => static::formatDescription($state, $record)),
                TextColumn::make('subject')
                    ->label(__('admin_user.activity_log.subject'))
                    ->formatStateUsing(function ($state, $record) {
                        $subjectType = $record->subject_type;
                        $event = $record->event;
                        $unit = '';
                        switch ($subjectType) {
                            case 'App\Models\AdminUser':
                                $unit = __('admin_user.navigation.account_management');
                                break;
                            case 'App\Models\AdminUserTeam':
                                $unit = __('admin_user.navigation.team_management');
                                break;
                            case 'App\Models\AdminIpWhitelist':
                                $unit = __('admin_user.navigation.ip_white_list');
                                break;
                            case 'App\Models\Rate':
                                $unit = __('admin_user.navigation.rate_settings');
                                break;
                            case 'App\Models\Deposit':
                                $unit = __('admin_user.navigation.deposit');
                                break;
                            case 'App\Models\Withdraw':
                                $unit = __('admin_user.navigation.withdraw');
                                break;
                            case 'App\Models\DepositLocation':
                                $unit = __('admin_user.navigation.deposit_location_management');
                                break;
                            case 'App\Models\PlatformWallet':
                                $unit = __('admin_user.navigation.platform_wallet_management');
                                break;
                            case 'App\Models\User':
                                $unit = __('admin_user.navigation.user_management');
                                break;
                            case 'App\Models\Wallet':
                                $unit = __('admin_user.navigation.wallet_management');
                                break;
                            case 'Spatie\Permission\Models\Role':
                                $unit = __('admin_user.navigation.role_management');
                                break;
                            default:
                                $unit = '其他';
                        }


                        // 從語系取出對應文字
                        $module = __('activity.subject_modules.' . $subjectType);
                        $action = __('activity.event_names.' . $event);
                        // 如果沒有找到語系就顯示原始型別
                        $module = $module !== "activity.subject_modules.$subjectType" ? $module : class_basename($subjectType);
                        $action = $action !== "activity.event_names.deleted" ? $action : $event;

                        return "{$module} -{$unit}- {$action}";
                    }),

                TextColumn::make('created_at')->label(__('admin_user.activity_log.time'))->since(),
            ])

            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('causer_id')
                    ->label(__('admin_user.activity_log.causer'))
                    ->options(
                        \App\Models\AdminUser::pluck('name', 'id')
                    )
                    ->default(request('causer_id')),
                //
            ], layout: FiltersLayout::AboveContent)
            ->actions([]);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdminLogs::route('/'),
            'create' => Pages\CreateAdminLog::route('/create'),
        ];
    }
    // public static function getEloquentQuery(): Builder
    // {
    //     $query = parent::getEloquentQuery();
    /**
     * 將 activity log 的 description 欄位格式化顯示
     */
    protected static function formatDescription($state, $record)
    {
        $causerName = optional($record->causer)->name ?? '未知操作者';
        // 操作對象
        $subject = $record->subject;
        $event = $record->event;
        // 異動的資的
        $properties = $record->properties;

        // 看是看是在哪個做操作，客製不同訊息
        switch (true) {
            case $subject instanceof \App\Models\Rate:
                return static::formatRateDescription($causerName, $subject, $event, $properties);
            case $subject instanceof \App\Models\Deposit:
                return static::formatDepositDescription($causerName, $subject, $event, $properties);
            case $subject instanceof \App\Models\Withdraw:
                return static::formatWithdrawDescription($causerName, $subject, $event, $properties);
            case $subject instanceof \App\Models\DepositLocation:
                return static::formatDepositLocationDescription($causerName, $subject, $event, $properties);
            case $subject instanceof \App\Models\PlatformWallet:
                return static::formatPlatformWalletDescription($causerName, $subject, $event, $properties);
            case $subject instanceof \App\Models\User:
                return static::formatUserDescription($causerName, $subject, $event, $properties);
            case $subject instanceof \App\Models\AdminUser:
                return static::formatAdminUserDescription($causerName, $subject, $event, $properties);
            case $subject instanceof \App\Models\AdminUserTeam:
                return static::formatAdminUserTeamDescription($causerName, $subject, $event, $properties);
            case $subject instanceof \App\Models\AdminIpWhitelist:
                return static::formatAdminIpWhitelistDescription($causerName, $subject, $event, $properties, $record);
            case $subject instanceof \Spatie\Permission\Models\Role:
                return static::formatRoleDescription($causerName, $subject, $event, $properties);
            default:
                $subjectType = class_basename($record->subject_type);
                return __('activity.log_description', [
                    'causer' => $causerName,
                    'subject' => $subjectType,
                    'event' => $event,
                ]);
        }
    }

    // 針對 Rate
    protected static function formatRateDescription($causerName, $subject, $event, $properties)
    {
        $from = optional($subject->fromCurrency)->code ?? '未知';
        $to = optional($subject->toCurrency)->code ?? '未知';

        $details = [];

        if (isset($properties['attributes']['buy_rate'])) {
            $old = $properties['old']['buy_rate'] ?? 'N/A';
            $new = $properties['attributes']['buy_rate'];
            $details[] = __('activity.rate_buy_rate_change', compact('old', 'new'));
        }

        if (isset($properties['attributes']['sell_rate'])) {
            $old = $properties['old']['sell_rate'] ?? 'N/A';
            $new = $properties['attributes']['sell_rate'];
            $details[] = __('activity.rate_sell_rate_change', compact('old', 'new'));
        }

        return __('activity.rate_updated', [
            'causer' => $causerName,
            'event' => $event,
            'from' => $from,
            'to' => $to,
            'details' => implode('，', $details),
        ]);
    }

    // 針對 Deposit
    protected static function formatDepositDescription($causerName, $subject, $event, $properties)
    {
        $attributes = $properties['attributes'] ?? [];
        $old        = $properties['old'] ?? [];

        $member   = optional($subject->user)->name ?? '未知會員';
        $currency = $subject->currencyCode->code ?? '未知';

        // 操作入金事件
        if ($event === 'created') {
            $amount = $subject->amount;
            return __('activity.deposit_created', [
                'causer'   => $causerName,
                'member'   => $member,
                'amount'   => $amount,
                'currency' => $currency,
            ]);
        }


        // 以下僅處理 updated 事件
        // 狀態異動
        if (array_key_exists('status', $attributes)) {
            return __('activity.deposit_status_updated', [
                'causer'     => $causerName,
                'member'     => $member,
                'old_status' => self::mapDepositStatus($old['status'] ?? null),
                'new_status' => self::mapDepositStatus($attributes['status']),
            ]);
        }
        // 其餘情況
        return "操作員 {$causerName} 對會員 {$member} 進行了 {$event} 操作。";
    }

    // 針對 Withdraw
    protected static function formatWithdrawDescription($causerName, $subject, $event, $properties)
    {
        $properties = $properties ?? [];
        $attributes = $properties['attributes'] ?? [];
        $old = $properties['old'] ?? [];

        $member = optional($subject->user)->name ?? "未知會員";
        $currency = $subject->currencyCode->code ?? '未知';

        $statusMap = [
            '0' => __('deposit.status.pending'),
            '1' => __('deposit.status.success'),
            '2' => __('deposit.status.failed'),
        ];

        if ($event === 'created') {
            $amount = $subject->amount;
            return __('activity.withdraw_created', [
                'causer'   => $causerName,
                'member'   => $member,
                'amount'   => $amount,
                'currency' => $currency,
            ]);
        }

        if ($event === 'updated') {
            // 狀態異動
            if (array_key_exists('status', $attributes)) {
                $oldStatus = $statusMap[$old['status'] ?? ''] ?? ($old['status'] ?? '未知');
                $newStatus = $statusMap[$attributes['status']] ?? $attributes['status'];

                return __('activity.withdraw_status_updated', [
                    'causer'     => $causerName,
                    'member'     => $member,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ]);
            }
            if (array_key_exists('tx_hash', $attributes)) {
                $oldTxHash = $old['tx_hash'] ?? 'N/A';
                $newTxHash = $attributes['tx_hash'];
                return __('activity.withdraw_tx_hash_updated', [
                    'causer' => $causerName,
                    'member' => $member,
                    'old' => $oldTxHash,
                    'new' => $newTxHash,
                ]);
            }
        }

        return "操作員 {$causerName} 對會員 {$member} 進行了 {$event} 操作。";
    }

    // 針對 DepositLocation
    protected static function formatDepositLocationDescription($causerName, $subject, $event, $properties)
    {
        $attributes = $properties['attributes'] ?? [];
        $old = $properties['old'] ?? [];
        $fieldMap = __('activity.deposit_location_fields');
        $location = $attributes['location'] ?? $subject->location ?? '未知';

        if ($event === 'created') {
            return __('activity.deposit_location_created', [
                'causer' => $causerName,
                'location' => $location,
            ]);
        }

        if ($event === 'updated') {
            $changes = collect($attributes)->map(function ($newValue, $field) use ($old, $fieldMap) {
                $oldValue = $old[$field] ?? '（無）';
                $oldValue = DepositLocationStatus::tryFrom((string) $old[$field] ?? '')?->label() ?? $old[$field] ?? '（無）';
                $newValue = DepositLocationStatus::tryFrom((string) $newValue)?->label() ?? $newValue;
                $translatedField = $fieldMap[$field] ?? $field;
                return __('activity.deposit_location_change_line', [
                    'field' => $translatedField,
                    'old' => $oldValue,
                    'new' => $newValue,
                ]);
            })->implode('，');

            return __('activity.deposit_location_updated', [
                'causer' => $causerName,
                'changes' => $changes,
            ]);
        }

        return "操作員 {$causerName} 對入金地址資料進行了 {$event} 操作。";
    }

    // 針對 PlatformWallet
    protected static function formatPlatformWalletDescription($causerName, $subject, $event, $properties)
    {
        $member = optional($subject->user)->name ?? "未知會員";
        $amount = number_format($subject->amount, 2);
        return __('activity.platform_wallet_created', [
            'causer' => $causerName,
            'member' => $member,
            'amount' => $amount,
            'currency' => $subject->currencyCode->code ?? '未知',
        ]);
    }

    // 針對 User
    protected static function formatUserDescription($causerName, $subject, $event, $properties)
    {
        $attributes = $properties['attributes'] ?? [];
        $old = $properties['old'] ?? [];

        // 避免密碼等敏感欄位顯示在紀錄中
        unset($attributes['password'], $attributes['otp_secret']);
        unset($old['password'], $old['otp_secret']);

        $fieldMap = __('activity.user_fields');
        $memberName = $subject->name ?? $attributes['name'] ?? '未知會員';

        if ($event === 'created') {
            return __('activity.user_created', [
                'causer' => $causerName,
                'member' => $memberName,
            ]);
        }

        if ($event === 'updated') {
            $changes = collect($attributes)->map(function ($newValue, $field) use ($old, $fieldMap) {
                $oldValue = $old[$field] ?? '（無）';
                $translatedField = $fieldMap[$field] ?? $field;
                if ($field === 'status') {
                    $oldValue = match ((string) $oldValue) {
                        '1' => '正常',
                        '0' => '凍結',
                        default => $oldValue,
                    };
                    $newValue = match ((string) $newValue) {
                        '1' => '正常',
                        '0' => '凍結',
                        default => $newValue,
                    };
                }
                return __('activity.user_change_line', [
                    'field' => $translatedField,
                    'old' => $oldValue,
                    'new' => $newValue,
                ]);
            })->implode('，');

            return __('activity.user_updated', [
                'causer' => $causerName,
                'member' => $memberName,
                'changes' => $changes,
            ]);
        }

        return "操作員 {$causerName} 對會員 {$memberName} 進行了 {$event} 操作。";
    }

    // 針對 AdminUser
    protected static function formatAdminUserDescription($causerName, $subject, $event, $properties)
    {
        $member = $subject->name ?? "未知會員";
        $attributes = $properties['attributes'] ?? [];
        $propertyKeys = array_keys($attributes);
        $propertyText = implode('、', $propertyKeys) ?: '未知欄位';
        $oldData = $properties['old'] ?? [];
        $oldDataText = implode('、', $oldData);
        $newData = $properties['attributes'] ?? [];
        $newDataText = implode('、', $newData);

        return __('activity.admin_user_' . $event, [
            'causer' => $causerName,
            'member' => $member,
            'property' => __('activity.admin_user_property.' . $propertyText),
            'old' => $oldDataText,
            'new' => $newDataText,
        ]);
    }

    // 針對 AdminUserTeam
    protected static function formatAdminUserTeamDescription($causerName, $subject, $event, $properties)
    {
        $oldName = $properties['old']['name'] ?? null;
        $newName = $properties['attributes']['name'] ?? null;
        $oldDesc = $properties['old']['description'] ?? null;
        $newDesc = $properties['attributes']['description'] ?? null;

        $messages = [];
        // 假設是刪除
        if ($event == 'deleted') {
            return __('activity.admin_user_team_deleted', [
                'causer' => $causerName,
                'old_team' => $oldName ?? '未知',
            ]);
        }
        // 假設是新增
        if ($event == 'created') {
            return __('activity.admin_user_team_created', [
                'causer' => $causerName,
                'new_team' => $newName ?? '未知',
            ]);
        }

        if ($oldName !== $newName) {
            $messages[] = __('activity.admin_user_team_name_updated', [
                'old' => $oldName ?? '未知',
                'new' => $newName ?? '未知',
            ]);
        }

        if ($oldDesc !== $newDesc) {
            $messages[] = __('activity.admin_user_team_description_updated', [
                'old' => $oldDesc ?? '未知',
                'new' => $newDesc ?? '未知',
            ]);
        }

        if (empty($messages)) {
            return __('activity.admin_user_team_deleted', [
                'causer' => $causerName,
            ]);
        }

        return "操作員 {$causerName} " . implode('，', $messages) . '。';
    }

    // 針對 AdminIpWhitelist
    protected static function formatAdminIpWhitelistDescription($causerName, $subject, $event, $properties, $record)
    {
        $new_ip_address = $subject->ip_address ?? '未知IP';
        $old_ip_address = $properties['old']['ip_address'] ?? '未知IP';
        return __('activity.ip_white_list_' . $record->event, [
            'causer' => $causerName,
            'new_ip_address' => $new_ip_address,
            'old_ip_address' => $old_ip_address,
        ]);
    }

    // 針對 Role
    protected static function formatRoleDescription($causerName, $subject, $event, $properties)
    {
        $attributes = $properties['attributes'] ?? [];
        $old = $properties['old'] ?? [];
        $roleName = $subject->name ?? $attributes['name'] ?? '未知角色';

        if ($event === 'created') {
            $permissions = $properties['assigned_permissions'] ?? [];
            $mappedPermissions = collect($permissions)->map(function ($key) {
                $key = str_replace('::', '_', $key);
                $translated = __('permissions.' . $key);
                return $translated === 'permissions.' . $key ? $key : $translated;
            })->implode('、');
            return __('activity.role_created', [
                'causer' => $causerName,
                'role' => $roleName,
                'permissions' => $mappedPermissions,
            ]);
        }

        if ($event === 'updated') {
            $changes = collect($attributes)->map(function ($new, $field) use ($old) {
                $oldValue = $old[$field] ?? '（無）';
                return __('activity.role_change_line', [
                    'field' => $field,
                    'old' => $oldValue,
                    'new' => $new,
                ]);
            })->implode('，');

            return __('activity.role_updated', [
                'causer' => $causerName,
                'role' => $roleName,
                'changes' => $changes,
            ]);
        }

        // fallback
        return "操作員 {$causerName} 對角色 {$roleName} 進行了 {$event} 操作。";
    }

    /**
     * 將存款狀態碼轉換為文字
     */
    protected static function mapDepositStatus($status): string
    {
        return match ((string) $status) {
            '0' => __('activity.status.pending'),
            '1' => __('activity.status.success'),
            '2' => __('activity.status.failed'),
            default => $status ?? '未知',
        };
    }

    /**
     * 統一金額格式
     */
    protected static function money($amount): string
    {
        return number_format($amount ?? 0, 2);
    }
}
