<?php

namespace App\Filament\Resources\AdminUserResource\Pages;

use App\Filament\Resources\AdminUserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateAdminUser extends CreateRecord
{
    protected static string $resource = AdminUserResource::class;

    // 用於存儲角色ID
    protected array $roleIds = [];

    // 在創建記錄之前處理表單數據
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 保存角色ID以便稍後使用
        $this->roleIds = $data['roles'] ?? [];

        // 從表單數據中移除roles字段，因為我們將手動處理角色分配
        unset($data['roles']);

        return $data;
    }

    // 在記錄創建之後處理角色分配
    protected function afterCreate(): void
    {
        $adminUser = $this->record;

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
