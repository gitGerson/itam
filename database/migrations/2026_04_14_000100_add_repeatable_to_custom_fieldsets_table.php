<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_fieldsets', function (Blueprint $table): void {
            $table->boolean('repeatable')->default(false)->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('custom_fieldsets', function (Blueprint $table): void {
            $table->dropColumn('repeatable');
        });
    }
};
