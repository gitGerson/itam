<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CategoryModuleTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_with_category_view_permission_can_load_category_data(): void
    {
        $user = $this->createUserWithPermissions(['inventory.categories.view']);

        $this->actingAs($user)
            ->get(route('categories.data'))
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_user_with_category_create_permission_can_store_category(): void
    {
        Storage::fake('s3');

        $user = $this->createUserWithPermissions([
            'inventory.categories.view',
            'inventory.categories.create',
        ]);

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name' => 'Laptop',
            'category_type' => 'asset',
            'image' => UploadedFile::fake()->image('category.jpg'),
            'notes' => 'Main asset category',
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Laptop',
            'category_type' => 'asset',
            'created_by' => $user->id,
            'checkin_email' => true,
        ]);

        $category = Category::where('name', 'Laptop')->firstOrFail();
        $this->assertNotNull($category->image);
        Storage::disk('s3')->assertExists($category->image);
    }

    public function test_user_with_category_edit_permission_can_update_category(): void
    {
        Storage::fake('s3');

        $user = $this->createUserWithPermissions([
            'inventory.categories.view',
            'inventory.categories.edit',
        ]);

        $category = Category::factory()->create([
            'name' => 'Peripheral',
            'category_type' => 'accessory',
            'image' => 'categories/existing-image.jpg',
        ]);

        Storage::disk('s3')->put('categories/existing-image.jpg', 'old-image');

        $response = $this->actingAs($user)->put(route('categories.update', $category), [
            'name' => 'Peripheral Updated',
            'category_type' => 'component',
            'image' => UploadedFile::fake()->image('updated-category.jpg'),
            'notes' => 'Updated notes',
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Peripheral Updated',
            'category_type' => 'component',
            'updated_by' => $user->id,
            'checkin_email' => true,
        ]);

        $category->refresh();
        $this->assertNotSame('categories/existing-image.jpg', $category->image);
        Storage::disk('s3')->assertExists($category->image);
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
