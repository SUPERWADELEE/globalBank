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
                TextColumn::make('causer.name')->label(__('admin_user.activity_log.causer'))->searchable(),
                TextColumn::make('description')
                    ->label(__('admin_user.activity_log.description'))
                    ->formatStateUsing(function ($state, $record) {
                        $causerName = optional($record->causer)->name ?? '未知操作者';
                        $subject = $record->subject;
                        $causer = $record->causer;
                        $event = $record->event;
                        $properties = $record->properties;

                        switch (true) {
                            case $subject instanceof \App\Models\Rate:
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

                            case $subject instanceof \App\Models\Deposit:
                                $member = optional($subject->user)->name ?? "未知會員";
                                $amount = number_format($subject->amount, 2);
                                return __('activity.deposit_created', [
                                    'causer' => $causerName,
                                    'member' => $member,
                                    'amount' => $amount,
                                    'currency' => $subject->currencyCode->code ?? '未知',
                                ]);

                            case $subject instanceof \App\Models\Withdraw:
                                $member = optional($subject->user)->name ?? "未知會員";
                                $amount = number_format($subject->amount, 2);
                                return __('activity.withdraw_created', [
                                    'causer' => $causerName,
                                    'member' => $member,
                                    'amount' => $amount,
                                    'currency' => $subject->currencyCode->code ?? '未知',
                                ]);
                            case $subject instanceof \App\Models\DepositLocation:
                                $event = $record->event;
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
                                        // old newdata要用enum轉換
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



                            case $subject instanceof \App\Models\PlatformWallet:
                                $member = optional($subject->user)->name ?? "未知會員";
                                $amount = number_format($subject->amount, 2);
                                return __('activity.platform_wallet_created', [
                                    'causer' => $causerName,
                                    'member' => $member,
                                    'amount' => $amount,
                                    'currency' => $subject->currencyCode->code ?? '未知',
                                ]);

                            case $subject instanceof \App\Models\User:
                                $event = $record->event;
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



                            case $subject instanceof \App\Models\AdminUser:
                                $member = $subject->name ?? "未知會員";
                                $event = $record->event;

                                $attributes = $properties['attributes'] ?? [];

                                // 取得被更新的欄位 key 們
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

                            case $subject instanceof \App\Models\AdminUserTeam:
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

                                return __('activity.admin_user_team_' . $record->event, [
                                    'causer' => $causerName,
                                    'old_team' => $oldDataText,
                                    'new_team' => $newDataText,
                                    'old_description' => $oldDescriptionText,
                                    'new_description' => $newDescriptionText,
                                ]);

                            case $subject instanceof \App\Models\AdminIpWhitelist:
                                $new_ip_address = $subject->ip_address ?? '未知IP';
                                $old_ip_address = $properties['old']['ip_address'] ?? '未知IP';
                                return __('activity.ip_white_list_' . $record->event, [
                                    'causer' => $causerName,
                                    'new_ip_address' => $new_ip_address,
                                    'old_ip_address' => $old_ip_address,
                                ]);

                            case $subject instanceof \Spatie\Permission\Models\Role:
                                $event = $record->event;
                                $attributes = $properties['attributes'] ?? [];
                                $old = $properties['old'] ?? [];

                                $roleName = $subject->name ?? $attributes['name'] ?? '未知角色';

                                if ($event === 'created') {
                                    // 權限會寫在 properties 裡面
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
                                    // 欄位變更處理
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
                            default:
                                // dump($subject);
                                $subjectType = class_basename($record->subject_type);
                                return __('activity.log_description', [
                                    'causer' => $causerName,
                                    'subject' => $subjectType,
                                    'event' => $event,
                                ]);
                        }
                    }),
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

                TextColumn::make('created_at')->label(__('admin_user.activity_log.time'))->since()->searchable(),
            ])

            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('causer_id')
                    ->label(__('admin_user.activity_log.causer'))
                    ->options(
                        \App\Models\AdminUser::pluck('name', 'id')
                    ),
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
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (request()->filled('causer_id')) {
            $query->where('causer_id', request('causer_id'));
        }

        return $query;
    }
}
