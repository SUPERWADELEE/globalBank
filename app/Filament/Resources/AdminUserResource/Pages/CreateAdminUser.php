<?php

namespace App\Filament\Resources\AdminUserResource\Pages;

use App\Filament\Resources\AdminUserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateAdminUser extends CreateRecord
{
    protected static string $resource = AdminUserResource::class;

    // 用於存儲角色ID
    protected string|int|null $roleId = null;

  
    // 在創建記錄之前處理表單數據
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 保存角色ID以便稍後使用
        $this->roleId = $data['roles'] ?? null;

        // 從表單數據中移除roles字段，因為我們將手動處理角色分配
        unset($data['roles']);

        return $data;
    }

    // 在記錄創建之後處理角色分配
    protected function afterCreate(): void
    {
        $adminUser = $this->record;

        // 確保我們有角色要分配
        if (!empty($this->roleId)) {
            // 手動分配角色，確保model_type正確
            DB::table('model_has_roles')->insert([
                'role_id' => $this->roleId,
                'model_id' => $adminUser->id,
                'model_type' => 'App\\Models\\AdminUser'
            ]);
        }
    }
    public function getTitle(): string
    {
        return __('permissions.create_admin_user');
    }
    public function getHeading(): string
    {
        return __('system_management.title');
    }
    public function getSubheading(): string
    {
        return __('permissions.create_admin_user');
    }

}
