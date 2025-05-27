<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\AdminUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guardName = 'admin';

        // 創建 Super Admin 角色
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => $guardName,
        ]);

        // 獲取所有權限
        $allPermissions = Permission::where('guard_name', $guardName)->get();
        $superAdminRole->syncPermissions($allPermissions);
        $firstAdminUser = AdminUser::first();

        if ($firstAdminUser) {
            // 移除現有角色

            DB::table('model_has_roles')
                ->where('model_id', $firstAdminUser->id)
                ->where('model_type', 'App\\Models\\AdminUser')
                ->delete();

            // 分配 Super Admin 角色
            DB::table('model_has_roles')->insert([
                'role_id' => $superAdminRole->id,
                'model_id' => $firstAdminUser->id,
                'model_type' => 'App\\Models\\AdminUser'
            ]);
        }
        // 直接創建admin_user
        AdminUser::firstOrCreate([
            'name' => 'admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('aaaa1234'),
        ]);
        // 直接給他super_admin角色
        $adminUser = AdminUser::where('email', 'superadmin@example.com')->first();
        DB::table('model_has_roles')->insert([
            'role_id' => $superAdminRole->id,
            'model_id' => $adminUser->id,
            'model_type' => 'App\\Models\\AdminUser'
        ]);
    }
}
