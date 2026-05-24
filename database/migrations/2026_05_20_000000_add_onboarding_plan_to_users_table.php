<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('onboarding_plan')->nullable()->after('dashboard_theme');
        });

        DB::table('users')
            ->whereNotNull('dashboard_theme')
            ->whereNull('onboarding_plan')
            ->orderBy('id')
            ->get(['id', 'plan'])
            ->each(function ($user) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['onboarding_plan' => $user->plan ?: 'free']);
            });

        DB::table('stores')
            ->whereNull('onboarding_completed_at')
            ->where('onboarding_step', '>=', 3)
            ->increment('onboarding_step');
    }

    public function down(): void
    {
        DB::table('stores')
            ->whereNull('onboarding_completed_at')
            ->where('onboarding_step', '>=', 4)
            ->decrement('onboarding_step');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('onboarding_plan');
        });
    }
};
