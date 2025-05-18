<?php

namespace App\Filament\Resources\AdminIpWhiteListResource\Pages;

use App\Filament\Resources\AdminIpWhiteListResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAdminIpWhiteList extends CreateRecord
{
    protected static string $resource = AdminIpWhiteListResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['admin_user_id'] = auth('admin')->id(); // 假設你是使用 admin guard
        return $data;
    }
}
