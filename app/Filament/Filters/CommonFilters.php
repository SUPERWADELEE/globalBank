<?php

namespace App\Filament\Filters;

use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\TextInput;
use App\Models\AdminUser;

class CommonFilters
{
    /**
     * 單欄位模糊搜尋 Filter
     *
     * @param string $field 欄位名稱
     * @param string|null $label 欄位顯示名稱
     * @param string|null $placeholder 輸入框提示
     */
    public static function textLike(string $field, ?string $label = null, ?string $placeholder = null)
    {
        return Filter::make($field)
            ->form([
                TextInput::make($field)
                    ->label($label ?? $field)
                    ->placeholder($placeholder ?? '請輸入查詢關鍵字'),
            ])
            ->query(function ($query, array $data) use ($field) {
                return $query->when(
                    $data[$field] ?? null,
                    fn($query, $value) =>
                    $query->where($field, 'like', "%{$value}%")
                );
            });
    }

    public static function relationTextLike(
        string $relation,
        string $relationField,
        string $filterName,
        ?string $label = null,
        ?string $placeholder = null
    ) {
        // 檢查是否有 causer_id 參數並且是 causer_name 過濾器
        $defaultValue = null;
        if ($filterName === 'causer_name' && request()->has('causer_id')) {
            $causerId = request()->get('causer_id');
            $adminUser = AdminUser::find($causerId);
            if ($adminUser) {
                $defaultValue = $adminUser->name;
            }
        }

        return Filter::make($filterName)
            ->form([
                TextInput::make($filterName)
                    ->label($label ?? $filterName)
                    ->placeholder($placeholder ?? '請輸入查詢關鍵字')
                    ->default($defaultValue),
            ])
            ->query(function ($query, array $data) use ($relation, $relationField, $filterName) {
                return $query->when(
                    $data[$filterName] ?? null,
                    fn($query, $value) =>
                    $query->whereHas($relation, function ($q) use ($relationField, $value) {
                        $q->where($relationField, 'like', "%{$value}%");
                    })
                );
            });
    }
}
