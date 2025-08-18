<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User Management Permissions
            ['name' => 'users.view', 'display_name' => 'View Users', 'description' => 'Can view user list and details', 'module' => 'users'],
            ['name' => 'users.create', 'display_name' => 'Create Users', 'description' => 'Can create new users', 'module' => 'users'],
            ['name' => 'users.edit', 'display_name' => 'Edit Users', 'description' => 'Can edit existing users', 'module' => 'users'],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'description' => 'Can delete users (soft delete)', 'module' => 'users'],
            ['name' => 'users.restore', 'display_name' => 'Restore Users', 'description' => 'Can restore deleted users', 'module' => 'users'],
            ['name' => 'users.force_delete', 'display_name' => 'Force Delete Users', 'description' => 'Can permanently delete users', 'module' => 'users'],
            ['name' => 'users.logs', 'display_name' => 'View User Logs', 'description' => 'Can view user activity logs', 'module' => 'users'],
            
            // Role Management Permissions (for future)
            ['name' => 'roles.view', 'display_name' => 'View Roles', 'description' => 'Can view roles list and details', 'module' => 'roles'],
            ['name' => 'roles.create', 'display_name' => 'Create Roles', 'description' => 'Can create new roles', 'module' => 'roles'],
            ['name' => 'roles.edit', 'display_name' => 'Edit Roles', 'description' => 'Can edit existing roles', 'module' => 'roles'],
            ['name' => 'roles.delete', 'display_name' => 'Delete Roles', 'description' => 'Can delete roles', 'module' => 'roles'],
            
            // Permission Management Permissions (for future)
            ['name' => 'permissions.view', 'display_name' => 'View Permissions', 'description' => 'Can view permissions list', 'module' => 'permissions'],
            ['name' => 'permissions.create', 'display_name' => 'Create Permissions', 'description' => 'Can create new permissions', 'module' => 'permissions'],
            ['name' => 'permissions.edit', 'display_name' => 'Edit Permissions', 'description' => 'Can edit existing permissions', 'module' => 'permissions'],
            ['name' => 'permissions.delete', 'display_name' => 'Delete Permissions', 'description' => 'Can delete permissions', 'module' => 'permissions'],
            
            // Dashboard and General Permissions
            ['name' => 'dashboard.view', 'display_name' => 'View Dashboard', 'description' => 'Can access dashboard', 'module' => 'general'],
            ['name' => 'reports.view', 'display_name' => 'View Reports', 'description' => 'Can view reports', 'module' => 'reports'],
            ['name' => 'settings.view', 'display_name' => 'View Settings', 'description' => 'Can view system settings', 'module' => 'settings'],
            ['name' => 'settings.edit', 'display_name' => 'Edit Settings', 'description' => 'Can edit system settings', 'module' => 'settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Create roles
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Administrator',
                'description' => 'Has full access to all system features',
                'permissions' => Permission::all()->pluck('name')->toArray()
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Has access to user management and most features',
                'permissions' => [
                    'users.view', 'users.create', 'users.edit', 'users.delete', 'users.restore', 'users.logs',
                    'dashboard.view', 'reports.view', 'settings.view'
                ]
            ],
            [
                'name' => 'user_manager',
                'display_name' => 'User Manager',
                'description' => 'Can manage users but limited system access',
                'permissions' => [
                    'users.view', 'users.create', 'users.edit', 'users.logs',
                    'dashboard.view'
                ]
            ],
            [
                'name' => 'viewer',
                'display_name' => 'Viewer',
                'description' => 'Read-only access to most features',
                'permissions' => [
                    'users.view', 'dashboard.view', 'reports.view'
                ]
            ],
            [
                'name' => 'user',
                'display_name' => 'Regular User',
                'description' => 'Basic user with limited access',
                'permissions' => [
                    'dashboard.view'
                ]
            ]
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                [
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description']
                ]
            );

            // Assign permissions to role
            foreach ($roleData['permissions'] as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission && !$role->hasPermission($permission)) {
                    $role->assignPermission($permission);
                }
            }
        }

        // Assign super_admin role to first user if exists
        $firstUser = User::first();
        if ($firstUser) {
            $superAdminRole = Role::where('name', 'super_admin')->first();
            if ($superAdminRole && !$firstUser->hasRole($superAdminRole)) {
                $firstUser->assignRole($superAdminRole);
            }
        }

        $this->command->info('Roles and permissions seeded successfully!');
    }
}
