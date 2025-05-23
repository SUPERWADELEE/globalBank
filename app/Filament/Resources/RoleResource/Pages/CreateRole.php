<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;


class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;
    public function getHeading(): string
    {
        return __('system_management.title');
    }
    public function getSubheading(): string
    {
        return __('permissions.create_permission_group');
    }
    protected function afterCreate(): void
    {
        $role = $this->record;

        // 直接從關聯撈出目前儲存的 permissions
        $submittedPermissionNames = $role->permissions->pluck('name')->toArray();

        if (!empty($submittedPermissionNames)) {
            activity()
                ->performedOn($role)
                ->causedBy(Auth::user())
                ->withProperties([
                    'assigned_permissions' => $submittedPermissionNames,
                ])
                ->event('created')
                ->log('建立角色並設定權限：' . implode('、', $submittedPermissionNames));
        }
    }
}
