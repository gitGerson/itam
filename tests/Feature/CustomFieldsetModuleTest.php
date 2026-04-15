<?php

namespace Tests\Feature;

use App\Models\CustomField;
use App\Models\CustomFieldset;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CustomFieldsetModuleTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_with_required_permissions_can_store_fieldset_with_inline_custom_fields(): void
    {
        $user = $this->createUserWithPermissions([
            'settings.custom_fieldsets.create',
            'settings.custom_fieldsets.view',
            'settings.custom_fields.create',
        ]);
        $existingField = CustomField::factory()->create([
            'name' => 'Existing Asset Tag',
            'element' => 'text',
        ]);

        $response = $this->actingAs($user)->post(route('custom-fieldsets.store'), [
            'name' => 'Procurement Details',
            'notes' => 'Fieldset with inline fields',
            'repeatable' => '1',
            'fieldset_fields' => [
                [
                    'source' => 'existing',
                    'custom_field_id' => $existingField->id,
                ],
                [
                    'source' => 'new',
                    'name' => 'Purchase Date',
                    'element' => 'date',
                    'help_text' => 'Tanggal pembelian aset',
                    'show_in_email' => '1',
                ],
                [
                    'source' => 'new',
                    'name' => 'Warranty Status',
                    'element' => 'select',
                    'field_values' => "Active\nExpired",
                    'field_encrypted' => '1',
                ],
            ],
        ]);

        $response->assertRedirect(route('custom-fieldsets.index'));

        $fieldset = CustomFieldset::where('name', 'Procurement Details')->firstOrFail();
        $purchaseDate = CustomField::where('name', 'Purchase Date')->firstOrFail();
        $warrantyStatus = CustomField::where('name', 'Warranty Status')->firstOrFail();

        $this->assertTrue($fieldset->repeatable);
        $this->assertSame(
            [$existingField->id, $purchaseDate->id, $warrantyStatus->id],
            $fieldset->customFields()->pluck('custom_fields.id')->all(),
        );
        $this->assertTrue($warrantyStatus->field_encrypted);
        $this->assertTrue($purchaseDate->show_in_email);
        $this->assertSame("Active\nExpired", $warrantyStatus->field_values);
    }

    public function test_user_without_custom_field_create_permission_cannot_create_inline_custom_fields(): void
    {
        $user = $this->createUserWithPermissions([
            'settings.custom_fieldsets.create',
            'settings.custom_fieldsets.view',
        ]);

        $response = $this->actingAs($user)->from(route('custom-fieldsets.create'))->post(route('custom-fieldsets.store'), [
            'name' => 'Restricted Fieldset',
            'fieldset_fields' => [
                [
                    'source' => 'new',
                    'name' => 'Secret Field',
                    'element' => 'text',
                ],
            ],
        ]);

        $response->assertRedirect(route('custom-fieldsets.create'));
        $response->assertSessionHasErrors('fieldset_fields');
        $this->assertDatabaseMissing('custom_fieldsets', [
            'name' => 'Restricted Fieldset',
        ]);
        $this->assertDatabaseMissing('custom_fields', [
            'name' => 'Secret Field',
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
