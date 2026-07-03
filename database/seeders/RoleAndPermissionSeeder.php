<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Default Permissions
        $permissions = [
            'manage users',
            'manage roles',
            'manage templates',
            'manage letters',
            'manage contacts',
            'view activity logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Super admin gets all permissions
        $superAdminRole->syncPermissions(Permission::all());

        // Admin gets general manage permissions except roles
        $adminRole->syncPermissions([
            'manage users',
            'manage templates',
            'manage letters',
            'manage contacts',
            'view activity logs',
        ]);

        // Staff gets limited permissions
        $staffRole->syncPermissions([
            'manage letters',
            'manage contacts',
        ]);

        // Assign Role to user ID 1 as example
        $user = User::find(1);
        if ($user) {
            $user->assignRole('super_admin');
            // Jika Anda juga menyimpan di kolom `role`:
            $user->update(['role' => 'super_admin']);
        }
    }
}
