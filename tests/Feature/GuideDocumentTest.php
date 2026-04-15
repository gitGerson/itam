<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class GuideDocumentTest extends TestCase
{
    public function test_guide_documents_filepond_rule_for_permanent_images(): void
    {
        $guideContents = File::get(base_path('guide.md'));

        $this->assertStringContainsString('`uploads.load` is for temporary FilePond files only.', $guideContents);
        $this->assertStringContainsString(':existingFiles="$record?->imageUrl() ? [$record->imageUrl()] : []"', $guideContents);
        $this->assertStringContainsString('This avoids 404s from `/uploads/load` on edit pages such as the department form.', $guideContents);
    }
}
