<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('image_position')->default('center')->after('image_3');
            $table->string('image_2_position')->default('center')->after('image_position');
            $table->string('image_3_position')->default('center')->after('image_2_position');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'image_position',
                'image_2_position',
                'image_3_position',
            ]);
        });
    }
};
