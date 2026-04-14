<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table): void {
            $table->dropColumn([
                'address',
                'address2',
                'city',
                'state',
                'country',
                'zip',
                'phone',
                'fax',
                'manager_id',
                'currency',
                'ldap_ou',
                'image',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table): void {
            $table->text('address')->nullable()->after('parent_id');
            $table->string('address2')->nullable()->after('address');
            $table->string('city', 100)->nullable()->after('address2');
            $table->string('state', 100)->nullable()->after('city');
            $table->string('country', 100)->nullable()->after('state');
            $table->string('zip', 20)->nullable()->after('country');
            $table->string('phone', 20)->nullable()->after('zip');
            $table->string('fax', 20)->nullable()->after('phone');
            $table->unsignedBigInteger('manager_id')->nullable()->after('parent_id');
            $table->string('currency', 10)->nullable()->after('country');
            $table->string('ldap_ou')->nullable()->after('fax');
            $table->string('image')->nullable();
        });
    }
};
