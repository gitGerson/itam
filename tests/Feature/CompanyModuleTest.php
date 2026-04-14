<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompanyModuleTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_with_company_view_permission_can_load_company_data(): void
    {
        $user = $this->createUserWithPermissions(['settings.companies.view']);

        $this->actingAs($user)
            ->get(route('companies.data'))
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_user_with_company_create_permission_can_store_company(): void
    {
        Storage::fake('s3');

        $user = $this->createUserWithPermissions([
            'settings.companies.view',
            'settings.companies.create',
        ]);

        $response = $this->actingAs($user)->post(route('companies.store'), [
            'name' => 'Tong Tji Holding',
            'email' => 'holding@example.com',
            'phone' => '021-5551234',
            'fax' => '021-5551235',
            'image' => UploadedFile::fake()->image('logo.jpg'),
            'notes' => 'Primary holding company',
        ]);

        $response->assertRedirect(route('companies.index'));
        $this->assertDatabaseHas('companies', [
            'name' => 'Tong Tji Holding',
            'created_by' => $user->id,
        ]);
        $company = Company::where('name', 'Tong Tji Holding')->firstOrFail();
        $this->assertNotNull($company->image);
        Storage::disk('s3')->assertExists($company->image);
    }

    public function test_user_with_company_edit_permission_can_update_company(): void
    {
        Storage::fake('s3');

        $user = $this->createUserWithPermissions([
            'settings.companies.view',
            'settings.companies.edit',
        ]);

        $company = Company::factory()->create([
            'name' => 'Tong Tji Foods',
            'image' => 'companies/existing-logo.jpg',
        ]);

        Storage::disk('s3')->put('companies/existing-logo.jpg', 'old-image');

        $response = $this->actingAs($user)->put(route('companies.update', $company), [
            'name' => 'Tong Tji Foods Updated',
            'email' => 'foods@example.com',
            'phone' => '021-5550099',
            'fax' => null,
            'image' => UploadedFile::fake()->image('new-logo.jpg'),
            'notes' => 'Updated notes',
        ]);

        $response->assertRedirect(route('companies.index'));
        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'Tong Tji Foods Updated',
            'updated_by' => $user->id,
        ]);
        $company->refresh();
        $this->assertNotSame('companies/existing-logo.jpg', $company->image);
        Storage::disk('s3')->assertExists($company->image);
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
