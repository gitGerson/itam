<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Services\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PermissionSyncServiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_permission_sync_persists_parent_and_sort_order_metadata(): void
    {
        Permission::query()->delete();

        app(PermissionSyncService::class)->sync();

        $permission = Permission::where('name', 'inventory.categories.view')->firstOrFail();

        $this->assertSame('inventory.access', $permission->parent);
        $this->assertSame(6, $permission->sort_order);
    }
}
