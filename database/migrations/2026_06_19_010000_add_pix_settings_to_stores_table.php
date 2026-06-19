<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->boolean('pix_enabled')->default(false)->after('whatsapp');
            $table->string('pix_key_type', 20)->nullable()->after('pix_enabled');
            $table->string('pix_key')->nullable()->after('pix_key_type');
            $table->string('pix_receiver_name', 25)->nullable()->after('pix_key');
            $table->string('pix_receiver_city', 15)->nullable()->after('pix_receiver_name');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'pix_enabled',
                'pix_key_type',
                'pix_key',
                'pix_receiver_name',
                'pix_receiver_city',
            ]);
        });
    }
};
