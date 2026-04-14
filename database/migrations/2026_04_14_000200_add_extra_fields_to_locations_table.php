<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table): void {
            $table->string('address2')->nullable()->after('address');
            $table->string('fax', 20)->nullable()->after('phone');
            $table->string('currency', 10)->nullable()->after('country');
            $table->string('ldap_ou')->nullable()->after('fax');
            $table->text('notes')->nullable()->after('ldap_ou');
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table): void {
            $table->dropColumn(['address2', 'fax', 'currency', 'ldap_ou', 'notes']);
        });
    }
};
