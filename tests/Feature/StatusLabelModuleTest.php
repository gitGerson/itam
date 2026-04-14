<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\StatusLabel;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class StatusLabelModuleTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_with_status_label_view_permission_can_load_status_label_data(): void
    {
        $user = $this->createUserWithPermissions(['settings.status_labels.view']);

        $this->actingAs($user)
            ->get(route('status-labels.data'))
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_user_with_status_label_create_permission_can_store_status_label(): void
    {
        $user = $this->createUserWithPermissions([
            'settings.status_labels.view',
            'settings.status_labels.create',
        ]);

        $response = $this->actingAs($user)->post(route('status-labels.store'), [
            'name' => 'Ready to Deploy',
            'deployable' => '1',
            'pending' => '0',
            'archived' => '0',
            'show_in_nav' => '1',
            'default_label' => '1',
            'color' => '#22c55e',
            'notes' => 'Status untuk aset siap distribusi.',
        ]);

        $response->assertRedirect(route('status-labels.index'));
        $this->assertDatabaseHas('status_labels', [
            'name' => 'Ready to Deploy',
            'deployable' => true,
            'pending' => false,
            'archived' => false,
            'show_in_nav' => true,
            'default_label' => true,
            'color' => '#22c55e',
            'created_by' => $user->id,
        ]);
    }

    public function test_user_with_status_label_edit_permission_can_update_status_label(): void
    {
        $user = $this->createUserWithPermissions([
            'settings.status_labels.view',
            'settings.status_labels.edit',
        ]);

        $statusLabel = StatusLabel::factory()->create([
            'name' => 'Pending Repair',
            'deployable' => false,
            'pending' => true,
            'archived' => false,
            'show_in_nav' => true,
            'default_label' => false,
            'color' => '#f59e0b',
        ]);

        $response = $this->actingAs($user)->put(route('status-labels.update', $statusLabel), [
            'name' => 'Archived Repair',
            'archived' => '1',
            'color' => '#6b7280',
            'notes' => 'Status setelah perangkat diarsipkan.',
        ]);

        $response->assertRedirect(route('status-labels.index'));
        $this->assertDatabaseHas('status_labels', [
            'id' => $statusLabel->id,
            'name' => 'Archived Repair',
            'deployable' => false,
            'pending' => false,
            'archived' => true,
            'show_in_nav' => false,
            'default_label' => false,
            'color' => '#6b7280',
            'updated_by' => $user->id,
        ]);
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
