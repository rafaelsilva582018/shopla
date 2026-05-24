<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan',
        'status',
        'amount',
        'external_reference',
        'asaas_checkout_id',
        'asaas_subscription_id',
        'asaas_payment_id',
        'asaas_customer_id',
        'checkout_url',
        'raw_response',
        'last_webhook_payload',
        'paid_at',
        'canceled_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'raw_response' => 'array',
        'last_webhook_payload' => 'array',
        'paid_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
