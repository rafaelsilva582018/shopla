<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('system_settings')->updateOrInsert(
            ['key' => 'plans.free.limit'],
            ['value' => '10', 'updated_at' => now(), 'created_at' => now()]
        );
    }

    public function down(): void
    {
        DB::table('system_settings')
            ->where('key', 'plans.free.limit')
            ->update(['value' => '6', 'updated_at' => now()]);
    }
};
