<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_field_custom_fieldset', function (Blueprint $table) {
            $table->foreignId('custom_field_id')->constrained('custom_fields')->cascadeOnDelete();
            $table->foreignId('custom_fieldset_id')->constrained('custom_fieldsets')->cascadeOnDelete();
            $table->unsignedSmallInteger('order')->default(0);

            $table->primary(['custom_field_id', 'custom_fieldset_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_field_custom_fieldset');
    }
};
