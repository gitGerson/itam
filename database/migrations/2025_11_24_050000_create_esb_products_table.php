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
        Schema::create('esb_products', function (Blueprint $table) {
            $table->id();
            $table->string('product_id')->unique();
            $table->string('product_code')->nullable();
            $table->string('product_name')->nullable();
            $table->string('category_id')->nullable();
            $table->string('category_name')->nullable();
            $table->string('sub_category_id')->nullable();
            $table->string('sub_category_name')->nullable();
            $table->string('bill_of_material_id')->nullable();
            $table->string('bill_of_material_code')->nullable();
            $table->string('bill_of_material_name')->nullable();
            $table->string('requestable')->nullable();
            $table->string('purchasable')->nullable();
            $table->string('vat')->nullable();
            $table->integer('receipt_tolerance')->nullable();
            $table->string('saleable')->nullable();
            $table->text('notes')->nullable();
            $table->string('created_date')->nullable();
            $table->string('created_by')->nullable();
            $table->string('edited_date')->nullable();
            $table->string('edited_by')->nullable();
            $table->json('product_details')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('esb_products');
    }
};
