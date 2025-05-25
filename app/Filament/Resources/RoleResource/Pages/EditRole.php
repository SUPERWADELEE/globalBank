<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;
    protected $originalPermissions;

    public function getHeading(): string
    {
        return __('system_management.title');
    }
    public function getSubheading(): string
    {
        return __('permissions.update_permission');
    }
    protected function getHeaderActions(): array
    {
        return [];
    }
    protected function beforeSave(): void
    {
        $this->originalPermissions = $this->record->permissions->pluck('name')->toArray();
    }
    protected function afterSave(): void
    {
        $role = $this->record->fresh();

        $newPermissions = $role->permissions->pluck('name')->toArray();
        $added = array_values(array_diff($newPermissions, $this->originalPermissions));
        $removed = array_values(array_diff($this->originalPermissions, $newPermissions));

        if ($added || $removed) {
            activity()
                ->performedOn($role)
                ->causedBy(Auth::user())
                ->withProperties([
                    'added_permissions' => $added,
                    'removed_permissions' => $removed,
                ])
                ->event('updated')
                ->log(
                    implode('；', [
                        $added ? __('role.permission_added') . implode('、', $added) : '',
                        $removed ? __('role.permission_removed') . implode('、', $removed) : '',
                    ])
                );
        }
    }
}
