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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address', 250)->nullable();
            $table->string('address2', 250)->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('phone', 35)->nullable();
            $table->string('fax', 35)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('contact', 100)->nullable();
            $table->string('notes', 191)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('zip', 10)->nullable();
            $table->string('url', 250)->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
