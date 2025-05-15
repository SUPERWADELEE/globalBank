<?php

namespace App\Filament\Resources\AdminUserResource\Pages;

use App\Filament\Resources\AdminUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use App\Services\AdminLogService;

class EditAdminUser extends EditRecord
{
    protected static string $resource = AdminUserResource::class;

    // 用於存儲角色ID
    protected string|int|null $roleId = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // 在更新記錄之前處理表單數據
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // 單選情況下，$data['roles'] 是字串或整數
        $this->roleId = is_array($data['roles']) ? $data['roles'][0] ?? null : $data['roles'];


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

        if (!empty($this->roleId)) {
            // 插入單一角色
            DB::table('model_has_roles')->insert([
                'role_id' => $this->roleId,
                'model_id' => $adminUser->id,
                'model_type' => 'App\\Models\\AdminUser'
            ]);
        }
        AdminLogService::log('update', 'admin_user', $this->record); 
    }
}
