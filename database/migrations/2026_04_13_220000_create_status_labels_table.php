<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('status_labels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('deployable')->default(false);
            $table->boolean('pending')->default(false);
            $table->boolean('archived')->default(false);
            $table->text('notes')->nullable();
            $table->string('color', 10)->nullable();
            $table->boolean('show_in_nav')->default(false);
            $table->boolean('default_label')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['archived', 'deployable', 'pending']);
            $table->index('show_in_nav');
            $table->index('default_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_labels');
    }
};
