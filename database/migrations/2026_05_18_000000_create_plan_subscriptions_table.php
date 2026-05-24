<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('plan');
            $table->string('status')->default('pending');
            $table->decimal('amount', 10, 2);
            $table->string('external_reference')->unique();
            $table->string('asaas_checkout_id')->nullable()->index();
            $table->string('asaas_subscription_id')->nullable()->index();
            $table->string('asaas_payment_id')->nullable()->index();
            $table->string('asaas_customer_id')->nullable()->index();
            $table->string('checkout_url')->nullable();
            $table->json('raw_response')->nullable();
            $table->json('last_webhook_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_subscriptions');
    }
};
