<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asaas_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_key')->unique();
            $table->string('event')->index();
            $table->string('resource_type')->nullable();
            $table->string('resource_id')->nullable()->index();
            $table->json('payload');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asaas_webhook_events');
    }
};
