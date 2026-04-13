<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('element')->default('text');
            $table->string('format')->nullable();
            $table->text('field_values')->nullable();
            $table->text('help_text')->nullable();
            $table->boolean('field_encrypted')->default(false);
            $table->boolean('show_in_email')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['element', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_fields');
    }
};
