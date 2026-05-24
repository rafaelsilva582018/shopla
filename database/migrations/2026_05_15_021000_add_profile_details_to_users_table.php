<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_name')->nullable()->after('name');
            $table->string('phone')->nullable()->after('email');
            $table->date('birthdate')->nullable()->after('phone');
            $table->string('document')->nullable()->after('birthdate');
            $table->string('zip_code', 20)->nullable()->after('state');
            $table->string('address')->nullable()->after('zip_code');
            $table->string('address_number', 30)->nullable()->after('address');
            $table->string('address_complement')->nullable()->after('address_number');
            $table->string('district')->nullable()->after('address_complement');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_name',
                'phone',
                'birthdate',
                'document',
                'zip_code',
                'address',
                'address_number',
                'address_complement',
                'district',
            ]);
        });
    }
};
