<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class UserPermissionFlowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_updating_user_permissions_preserves_existing_template_roles(): void
    {
        $actor = $this->createUserWithPermissions(['management.users.permissions']);
        $user = User::factory()->create();

        $templateRole = Role::create([
            'name' => 'admin_template',
            'display_name' => 'Administrator Template',
            'description' => 'Admin template',
        ]);

        $user->assignRole($templateRole);

        $permission = Permission::firstOrCreate(
            ['name' => 'settings.categories.view'],
            [
                'display_name' => 'Categories - View',
                'description' => 'Can view categories',
                'module' => 'settings',
                'is_active' => true,
            ],
        );

        $response = $this->actingAs($actor)->put(route('users.update-permissions', $user), [
            'permissions' => [$permission->id],
        ]);

        $response->assertRedirect(route('users.edit', $user));
        $user->refresh();

        $this->assertTrue($user->hasRole('admin_template'));
        $this->assertTrue($user->hasRole($user->dynamicPermissionRoleName()));
    }

    public function test_applying_role_template_preserves_dynamic_permission_role(): void
    {
        $actor = $this->createUserWithPermissions(['management.users.permissions']);
        $user = User::factory()->create();

        $templateRole = Role::create([
            'name' => 'viewer_template',
            'display_name' => 'Viewer Template',
            'description' => 'Viewer template',
        ]);

        $dynamicRole = Role::create([
            'name' => $user->dynamicPermissionRoleName(),
            'display_name' => 'Custom Permissions for '.$user->name,
            'description' => 'Dynamic role',
        ]);

        $user->assignRole($dynamicRole);

        $response = $this->actingAs($actor)->post(route('users.apply-template', $user), [
            'role_template' => $templateRole->id,
        ]);

        $response->assertRedirect(route('users.edit', $user));
        $user->refresh();

        $this->assertTrue($user->hasRole('viewer_template'));
        $this->assertTrue($user->hasRole($user->dynamicPermissionRoleName()));
    }

    private function createUserWithPermissions(array $permissions): User
    {
        $user = User::factory()->create();

        $role = Role::create([
            'name' => fake()->unique()->slug(),
            'display_name' => fake()->words(2, true),
            'description' => fake()->sentence(),
        ]);

        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName],
                [
                    'display_name' => $permissionName,
                    'description' => $permissionName,
                    'module' => str($permissionName)->before('.')->value(),
                    'is_active' => true,
                ],
            );

            $role->permissions()->syncWithoutDetaching([$permission->id]);
        }

        $user->roles()->attach($role->id, [
            'assigned_by' => $user->id,
            'assigned_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $user->fresh();
    }
}
