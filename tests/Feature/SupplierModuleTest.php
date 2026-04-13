<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SupplierModuleTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_with_supplier_view_permission_can_load_supplier_data(): void
    {
        $user = $this->createUserWithPermissions(['inventory.suppliers.view']);

        $this->actingAs($user)
            ->get(route('suppliers.data'))
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_user_with_supplier_create_permission_can_store_supplier(): void
    {
        Storage::fake('s3');

        $user = $this->createUserWithPermissions([
            'inventory.suppliers.view',
            'inventory.suppliers.create',
        ]);

        $response = $this->actingAs($user)->post(route('suppliers.store'), [
            'name' => 'PT Sumber Data Nusantara',
            'address' => 'Jl. Sudirman No. 1',
            'address2' => 'Gedung A Lt. 5',
            'city' => 'Jakarta',
            'state' => 'DKI Jakarta',
            'country' => 'id',
            'phone' => '021-123456',
            'fax' => '021-654321',
            'email' => 'procurement@example.com',
            'contact' => 'Budi Santoso',
            'notes' => 'Preferred network equipment supplier',
            'zip' => '10220',
            'url' => 'https://supplier.example.com',
            'image' => UploadedFile::fake()->image('logo.jpg'),
        ]);

        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('suppliers', [
            'name' => 'PT Sumber Data Nusantara',
            'created_by' => $user->id,
            'country' => 'ID',
        ]);

        $supplier = Supplier::where('name', 'PT Sumber Data Nusantara')->firstOrFail();
        $this->assertNotNull($supplier->image);
        Storage::disk('s3')->assertExists($supplier->image);
    }

    public function test_user_with_supplier_edit_permission_can_update_supplier(): void
    {
        Storage::fake('s3');

        $user = $this->createUserWithPermissions([
            'inventory.suppliers.view',
            'inventory.suppliers.edit',
        ]);

        $supplier = Supplier::factory()->create([
            'name' => 'PT Lama Teknologi',
            'image' => 'suppliers/existing-logo.jpg',
        ]);

        Storage::disk('s3')->put('suppliers/existing-logo.jpg', 'old-image');

        $response = $this->actingAs($user)->put(route('suppliers.update', $supplier), [
            'name' => 'PT Baru Teknologi',
            'address' => 'Jl. Asia Afrika No. 10',
            'address2' => 'Suite 12',
            'city' => 'Bandung',
            'state' => 'Jawa Barat',
            'country' => 'id',
            'phone' => '022-123456',
            'fax' => '022-654321',
            'email' => 'sales@example.com',
            'contact' => 'Sari Wulandari',
            'notes' => 'Updated supplier notes',
            'zip' => '40111',
            'url' => 'https://updated-supplier.example.com',
            'image' => UploadedFile::fake()->image('new-logo.jpg'),
        ]);

        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'PT Baru Teknologi',
            'updated_by' => $user->id,
            'country' => 'ID',
        ]);

        $supplier->refresh();
        $this->assertNotSame('suppliers/existing-logo.jpg', $supplier->image);
        Storage::disk('s3')->assertExists($supplier->image);
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
