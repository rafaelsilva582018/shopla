<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('store_card_color')->nullable()->after('text_color');
            $table->string('store_muted_color')->nullable()->after('store_card_color');
            $table->string('store_border_color')->nullable()->after('store_muted_color');
            $table->string('store_badge_color')->nullable()->after('store_border_color');
            $table->string('store_badge_text_color')->nullable()->after('store_badge_color');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'store_card_color',
                'store_muted_color',
                'store_border_color',
                'store_badge_color',
                'store_badge_text_color',
            ]);
        });
    }
};
