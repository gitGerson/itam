<?php

namespace Tests\Feature;

use App\Models\CustomField;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CustomFieldModuleTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_can_store_custom_field_without_format_column(): void
    {
        $user = $this->createUserWithPermissions([
            'settings.custom_fields.create',
            'settings.custom_fields.view',
        ]);

        $response = $this->actingAs($user)->post(route('custom-fields.store'), [
            'name' => 'Asset Condition',
            'element' => 'select',
            'field_values' => "Good\nDamaged",
            'help_text' => 'Kondisi aset saat ini',
            'show_in_email' => '1',
        ]);

        $response->assertRedirect(route('custom-fields.index'));

        $customField = CustomField::where('name', 'Asset Condition')->firstOrFail();

        $this->assertSame('select', $customField->element);
        $this->assertSame("Good\nDamaged", $customField->field_values);
        $this->assertTrue($customField->show_in_email);
        $this->assertFalse(isset($customField->getAttributes()['format']));
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
