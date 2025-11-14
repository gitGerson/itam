<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        $this->command->info('Clearing existing roles, permissions, and user assignments...');

        // Disable foreign key checks temporarily
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');

        \DB::table('user_role')->delete();
        \DB::table('role_permission')->delete();
        Role::truncate();
        Permission::truncate();

        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Create permissions following menu hierarchy structure
        $this->command->info('Creating permissions...');
        $permissions = [

            // === MANAGEMENT (Header) ===
            ['name' => 'management.access', 'display_name' => 'Management', 'description' => 'Can access Management section', 'module' => 'management', 'parent' => null, 'sort_order' => 2],

            // Management > Users
            ['name' => 'management.users.view', 'display_name' => 'Users - View', 'description' => 'Can view users', 'module' => 'management', 'parent' => 'management.access', 'sort_order' => 1],
            ['name' => 'management.users.create', 'display_name' => 'Users - Create', 'description' => 'Can create users', 'module' => 'management', 'parent' => 'management.access', 'sort_order' => 2],
            ['name' => 'management.users.edit', 'display_name' => 'Users - Edit', 'description' => 'Can edit users', 'module' => 'management', 'parent' => 'management.access', 'sort_order' => 3],
            ['name' => 'management.users.delete', 'display_name' => 'Users - Delete', 'description' => 'Can delete users', 'module' => 'management', 'parent' => 'management.access', 'sort_order' => 4],
            ['name' => 'management.users.restore', 'display_name' => 'Users - Restore', 'description' => 'Can restore deleted users', 'module' => 'management', 'parent' => 'management.access', 'sort_order' => 5],
            ['name' => 'management.users.force_delete', 'display_name' => 'Users - Force Delete', 'description' => 'Can permanently delete users', 'module' => 'management', 'parent' => 'management.access', 'sort_order' => 6],
            ['name' => 'management.users.logs', 'display_name' => 'Users - View Logs', 'description' => 'Can view user activity logs', 'module' => 'management', 'parent' => 'management.access', 'sort_order' => 7],
            ['name' => 'management.users.permissions', 'display_name' => 'Users - Manage Permissions', 'description' => 'Can manage user permissions directly', 'module' => 'management', 'parent' => 'management.access', 'sort_order' => 8],

            // Management > Roles (Templates)
            ['name' => 'management.roles.view', 'display_name' => 'Role Templates - View', 'description' => 'Can view role templates', 'module' => 'management', 'parent' => 'management.access', 'sort_order' => 9],
            ['name' => 'management.roles.create', 'display_name' => 'Role Templates - Create', 'description' => 'Can create role templates', 'module' => 'management', 'parent' => 'management.access', 'sort_order' => 10],
            ['name' => 'management.roles.edit', 'display_name' => 'Role Templates - Edit', 'description' => 'Can edit role templates', 'module' => 'management', 'parent' => 'management.access', 'sort_order' => 11],
            ['name' => 'management.roles.delete', 'display_name' => 'Role Templates - Delete', 'description' => 'Can delete role templates', 'module' => 'management', 'parent' => 'management.access', 'sort_order' => 12],

            // === IMAGES ===
            ['name' => 'images.access', 'display_name' => 'Images', 'description' => 'Can access Images section', 'module' => 'images', 'parent' => null, 'sort_order' => 3],
            ['name' => 'images.view', 'display_name' => 'Images - View', 'description' => 'Can view images', 'module' => 'images', 'parent' => 'images.access', 'sort_order' => 1],
            ['name' => 'images.create', 'display_name' => 'Images - Upload', 'description' => 'Can upload images', 'module' => 'images', 'parent' => 'images.access', 'sort_order' => 2],
            ['name' => 'images.edit', 'display_name' => 'Images - Edit', 'description' => 'Can edit images', 'module' => 'images', 'parent' => 'images.access', 'sort_order' => 3],
            ['name' => 'images.delete', 'display_name' => 'Images - Delete', 'description' => 'Can delete images', 'module' => 'images', 'parent' => 'images.access', 'sort_order' => 4],
            ['name' => 'images.restore', 'display_name' => 'Images - Restore', 'description' => 'Can restore deleted images', 'module' => 'images', 'parent' => 'images.access', 'sort_order' => 5],
            ['name' => 'images.force_delete', 'display_name' => 'Images - Force Delete', 'description' => 'Can permanently delete images', 'module' => 'images', 'parent' => 'images.access', 'sort_order' => 6],

        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create role templates for user creation
        $this->command->info('Creating role templates...');
        $roles = [
            [
                'name' => 'super_admin_template',
                'display_name' => 'Super Administrator Template',
                'description' => 'Template with full system access - all permissions',
                'permissions' => Permission::all()->pluck('name')->toArray()
            ],
            [
                'name' => 'admin_template',
                'display_name' => 'Administrator Template',
                'description' => 'Template with management access and most features',
                'permissions' => [
                    'dashboard.view',
                    'management.access',
                    'management.users.view',
                    'management.users.create',
                    'management.users.edit',
                    'management.users.delete',
                    'management.users.restore',
                    'management.users.logs',
                    'management.roles.view',
                    'management.roles.create',
                    'management.roles.edit',
                    'images.access',
                    'images.view',
                    'images.create',
                    'images.edit',
                    'images.delete',
                    'images.restore',
                ]
            ],
            [
                'name' => 'user_manager_template',
                'display_name' => 'User Manager Template',
                'description' => 'Template for user management focus',
                'permissions' => [
                    'dashboard.view',
                    'management.access',
                    'management.users.view',
                    'management.users.create',
                    'management.users.edit',
                    'management.users.logs',
                    'management.roles.view',
                ]
            ],
            [
                'name' => 'viewer_template',
                'display_name' => 'Viewer Template',
                'description' => 'Template for read-only access',
                'permissions' => [
                    'dashboard.view',
                    'management.access',
                    'management.users.view',
                ]
            ]
        ];

        foreach ($roles as $roleData) {
            $role = Role::create([
                'name' => $roleData['name'],
                'display_name' => $roleData['display_name'],
                'description' => $roleData['description']
            ]);

            // Assign permissions to role
            foreach ($roleData['permissions'] as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission && !$role->hasPermission($permission)) {
                    $role->assignPermission($permission);
                }
            }
        }

        // Create fresh stagingpurpose superadmin user
        $this->command->info('Creating fresh stagingpurpose superadmin user...');

        // Delete existing stagingpurpose user if exists
        User::where('username', 'stagingpurpose')->forceDelete();

        // Create new stagingpurpose user
        $superAdminUser = User::create([
            'name' => 'STAGING PURPOSE',
            'username' => 'stagingpurpose',
            'email' => 'staging@tongtji.com',
            'password' => Hash::make('P@ssw0rd1938'),
            'email_verified_at' => now(),
            'created_by' => null, // No creator for initial superadmin
            'updated_by' => null,
        ]);

        // Assign super_admin_template role to stagingpurpose user
        $superAdminRole = Role::where('name', 'super_admin_template')->first();
        if ($superAdminRole) {
            $superAdminUser->assignRole($superAdminRole);
        }

        $this->command->info('Fresh roles, permissions, and stagingpurpose superadmin created successfully!');
        $this->command->info('Superadmin credentials:');
        $this->command->info('Username: stagingpurpose');
        $this->command->info('Password: P@ssw0rd1938');
        $this->command->info('PIN: 123456');
    }
}
