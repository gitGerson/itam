<?php

namespace Tests\Feature;

use Tests\TestCase;

class MenuConfigTest extends TestCase
{
    public function test_manufacturers_menu_uses_a_supported_boxicon(): void
    {
        $manufacturersMenu = collect(config('menu.items'))
            ->firstWhere('key', 'manufacturers-index');

        $this->assertNotNull($manufacturersMenu);
        $this->assertSame('bx-buildings', $manufacturersMenu['icon']);
        $this->assertNotSame('bx-factory', $manufacturersMenu['icon']);
    }
}
