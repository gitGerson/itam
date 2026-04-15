<?php

namespace Tests\Feature;

use App\Models\AssetModel;
use App\Models\Category;
use App\Models\CustomFieldset;
use App\Models\Manufacturer;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssetModelModuleTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_with_model_view_permission_can_load_model_data(): void
    {
        $user = $this->createUserWithPermissions(['settings.models.view']);

        $this->actingAs($user)
            ->get(route('models.data'))
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_user_with_model_create_permission_can_store_model(): void
    {
        Storage::fake('s3');

        $user = $this->createUserWithPermissions([
            'settings.models.view',
            'settings.models.create',
        ]);

        $manufacturer = Manufacturer::factory()->create();
        $category = Category::factory()->create();
        $fieldset = CustomFieldset::factory()->create();

        $response = $this->actingAs($user)->post(route('models.store'), [
            'name' => 'ThinkPad X1 Carbon Gen 12',
            'model_number' => '21KC0001ID',
            'manufacturer_id' => $manufacturer->id,
            'category_id' => $category->id,
            'fieldset_id' => $fieldset->id,
            'eol' => 60,
            'image' => UploadedFile::fake()->image('model.jpg'),
            'notes' => 'Ultrabook standard issue',
        ]);

        $response->assertRedirect(route('models.index'));
        $this->assertDatabaseHas('models', [
            'name' => 'ThinkPad X1 Carbon Gen 12',
            'manufacturer_id' => $manufacturer->id,
            'category_id' => $category->id,
            'fieldset_id' => $fieldset->id,
            'created_by' => $user->id,
        ]);

        $assetModel = AssetModel::where('name', 'ThinkPad X1 Carbon Gen 12')->firstOrFail();
        $this->assertNotNull($assetModel->image);
        Storage::disk('s3')->assertExists($assetModel->image);
    }

    public function test_user_with_model_edit_permission_can_update_model(): void
    {
        Storage::fake('s3');

        $user = $this->createUserWithPermissions([
            'settings.models.view',
            'settings.models.edit',
        ]);

        $manufacturer = Manufacturer::factory()->create();
        $replacementManufacturer = Manufacturer::factory()->create();
        $category = Category::factory()->create();
        $replacementCategory = Category::factory()->create();
        $fieldset = CustomFieldset::factory()->create();
        $replacementFieldset = CustomFieldset::factory()->create();

        $assetModel = AssetModel::factory()->create([
            'name' => 'Old Model Name',
            'manufacturer_id' => $manufacturer->id,
            'category_id' => $category->id,
            'fieldset_id' => $fieldset->id,
            'image' => 'models/existing-model.jpg',
        ]);

        Storage::disk('s3')->put('models/existing-model.jpg', 'old-image');

        $response = $this->actingAs($user)->put(route('models.update', $assetModel), [
            'name' => 'Updated Model Name',
            'model_number' => 'UPD-2026',
            'manufacturer_id' => $replacementManufacturer->id,
            'category_id' => $replacementCategory->id,
            'fieldset_id' => $replacementFieldset->id,
            'eol' => 36,
            'image' => UploadedFile::fake()->image('new-model.jpg'),
            'notes' => 'Updated model notes',
        ]);

        $response->assertRedirect(route('models.index'));
        $this->assertDatabaseHas('models', [
            'id' => $assetModel->id,
            'name' => 'Updated Model Name',
            'manufacturer_id' => $replacementManufacturer->id,
            'category_id' => $replacementCategory->id,
            'fieldset_id' => $replacementFieldset->id,
            'updated_by' => $user->id,
        ]);

        $assetModel->refresh();
        $this->assertNotSame('models/existing-model.jpg', $assetModel->image);
        Storage::disk('s3')->assertExists($assetModel->image);
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
