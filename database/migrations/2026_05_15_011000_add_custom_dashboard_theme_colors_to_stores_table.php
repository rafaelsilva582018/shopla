<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('dashboard_bg_color')->nullable()->after('dashboard_theme');
            $table->string('dashboard_card_color')->nullable()->after('dashboard_bg_color');
            $table->string('dashboard_primary_color')->nullable()->after('dashboard_card_color');
            $table->string('dashboard_secondary_color')->nullable()->after('dashboard_primary_color');
            $table->string('dashboard_text_color')->nullable()->after('dashboard_secondary_color');
            $table->string('dashboard_muted_color')->nullable()->after('dashboard_text_color');
            $table->string('dashboard_border_color')->nullable()->after('dashboard_muted_color');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'dashboard_bg_color',
                'dashboard_card_color',
                'dashboard_primary_color',
                'dashboard_secondary_color',
                'dashboard_text_color',
                'dashboard_muted_color',
                'dashboard_border_color',
            ]);
        });
    }
};
