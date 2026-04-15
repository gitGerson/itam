<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class DepartmentModuleTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_department_form_uses_the_public_image_url_for_filepond_existing_files(): void
    {
        $viewContents = File::get(resource_path('views/departments/partials/form.blade.php'));

        $this->assertStringContainsString('$existingImageUrl = $department?->imageUrl();', $viewContents);
        $this->assertStringContainsString(':existingFiles="$existingImageUrl ? [$existingImageUrl] : []"', $viewContents);
        $this->assertStringNotContainsString(':existingFiles="isset($department) && $department->image ? [$department->image] : []"', $viewContents);
    }
}
