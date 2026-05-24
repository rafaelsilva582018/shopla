<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsaasWebhookEvent extends Model
{
    protected $fillable = [
        'event_key',
        'event',
        'resource_type',
        'resource_id',
        'payload',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime',
    ];
}
