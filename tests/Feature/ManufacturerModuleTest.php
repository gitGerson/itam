<?php

namespace Tests\Feature;

use App\Models\Manufacturer;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ManufacturerModuleTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_with_manufacturer_view_permission_can_load_manufacturer_data(): void
    {
        $user = $this->createUserWithPermissions(['inventory.manufacturers.view']);

        $this->actingAs($user)
            ->get(route('manufacturers.data'))
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_user_with_manufacturer_create_permission_can_store_manufacturer(): void
    {
        Storage::fake('s3');

        $user = $this->createUserWithPermissions([
            'inventory.manufacturers.view',
            'inventory.manufacturers.create',
        ]);

        $response = $this->actingAs($user)->post(route('manufacturers.store'), [
            'name' => 'Lenovo',
            'url' => 'https://www.lenovo.com',
            'support_url' => 'https://support.lenovo.com',
            'warranty_lookup_url' => 'https://pcsupport.lenovo.com/warrantylookup',
            'support_phone' => '021-123456',
            'support_email' => 'support@lenovo.example',
            'image' => UploadedFile::fake()->image('logo.jpg'),
            'notes' => 'Primary laptop manufacturer',
        ]);

        $response->assertRedirect(route('manufacturers.index'));
        $this->assertDatabaseHas('manufacturers', [
            'name' => 'Lenovo',
            'created_by' => $user->id,
            'checkin_email' => true,
        ]);

        $manufacturer = Manufacturer::where('name', 'Lenovo')->firstOrFail();
        $this->assertNotNull($manufacturer->image);
        Storage::disk('s3')->assertExists($manufacturer->image);
    }

    public function test_user_with_manufacturer_edit_permission_can_update_manufacturer(): void
    {
        Storage::fake('s3');

        $user = $this->createUserWithPermissions([
            'inventory.manufacturers.view',
            'inventory.manufacturers.edit',
        ]);

        $manufacturer = Manufacturer::factory()->create([
            'name' => 'Dell',
            'image' => 'manufacturers/existing-logo.jpg',
        ]);

        Storage::disk('s3')->put('manufacturers/existing-logo.jpg', 'old-image');

        $response = $this->actingAs($user)->put(route('manufacturers.update', $manufacturer), [
            'name' => 'Dell Updated',
            'url' => 'https://www.dell.com',
            'support_url' => 'https://www.dell.com/support',
            'warranty_lookup_url' => 'https://www.dell.com/warranty',
            'support_phone' => '021-654321',
            'support_email' => 'support@dell.example',
            'image' => UploadedFile::fake()->image('new-logo.jpg'),
            'notes' => 'Updated notes',
        ]);

        $response->assertRedirect(route('manufacturers.index'));
        $this->assertDatabaseHas('manufacturers', [
            'id' => $manufacturer->id,
            'name' => 'Dell Updated',
            'updated_by' => $user->id,
        ]);

        $manufacturer->refresh();
        $this->assertNotSame('manufacturers/existing-logo.jpg', $manufacturer->image);
        Storage::disk('s3')->assertExists($manufacturer->image);
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
