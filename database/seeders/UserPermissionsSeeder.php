<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guardName = 'admin';
        // 只新增一個權限
        $permission = Permission::firstOrCreate([
            'name' => 'view_user_qr_code',
            'guard_name' => $guardName,
        ]);
        // 只分配給 super_admin 角色
        $superAdminRole = Role::where('name', 'super_admin')
            ->where('guard_name', $guardName)
            ->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permission);
        }
    }
}
