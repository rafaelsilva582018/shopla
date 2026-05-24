<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('whatsapp')->nullable();
            $table->text('description')->nullable();

            $table->string('logo')->nullable();
            $table->string('banner')->nullable();

            $table->string('primary_color')->default('#6D28D9');
            $table->string('secondary_color')->default('#EC4899');
            $table->string('background_color')->default('#F9FAFB');
            $table->string('text_color')->default('#111827');

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
