<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function afterSave(): void
{
    $role = $this->record;

    $originalPermissions = $role->getOriginal('permissions') ?? [];
    $newPermissions = $role->permissions->pluck('name')->toArray();

    // 對照差異
    $added = array_diff($newPermissions, $originalPermissions);
    $removed = array_diff($originalPermissions, $newPermissions);

    $changes = [];

    if ($added) {
        $changes[] = __('role.permission_added') . implode('、', $added);
    }

    if ($removed) {
        $changes[] = __('role.permission_removed') . implode('、', $removed);
    }

    if (!empty($changes)) {
        activity()
            ->performedOn($role)
            ->causedBy(Auth::user())
            ->withProperties([
                'added_permissions' => $added,
                'removed_permissions' => $removed,
            ])
            ->event('updated')
            ->log(__('role.permission_updated_log') . implode('；', $changes));
    }
}
}
