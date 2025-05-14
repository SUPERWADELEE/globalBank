<?php

namespace App\Filament\Resources\AdminUserResource\Pages;

use App\Filament\Resources\AdminUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditAdminUser extends EditRecord
{
    protected static string $resource = AdminUserResource::class;

    // 用於存儲角色ID
    protected array $roleIds = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // 在更新記錄之前處理表單數據
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // 保存角色ID以便稍後使用
        $this->roleIds = $data['roles'] ?? [];

        // 從表單數據中移除roles字段，因為我們將手動處理角色分配
        unset($data['roles']);

        return $data;
    }

    // 在記錄更新之後處理角色分配
    protected function afterSave(): void
    {
        $adminUser = $this->record;

        // 刪除所有現有的角色分配
        DB::table('model_has_roles')
            ->where('model_id', $adminUser->id)
            ->where('model_type', 'App\\Models\\AdminUser')
            ->delete();

        // 確保我們有角色要分配
        if (!empty($this->roleIds)) {
            // 手動分配角色，確保model_type正確
            foreach ($this->roleIds as $roleId) {
                DB::table('model_has_roles')->insert([
                    'role_id' => $roleId,
                    'model_id' => $adminUser->id,
                    'model_type' => 'App\\Models\\AdminUser'
                ]);
            }
        }
    }
}
