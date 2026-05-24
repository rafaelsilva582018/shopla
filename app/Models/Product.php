<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'store_id',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'image',
        'image_2',
        'image_3',
        'image_position',
        'image_2_position',
        'image_3_position',
        'is_active',
        'is_featured',
        'availability_status',
        'stock_quantity',
        'track_stock',
    ];

    public const AVAILABILITY_STATUSES = [
        'sob_encomenda' => 'Sob encomenda',
        'pronta_entrega' => 'Pronta entrega',
        'esgotado' => 'Esgotado',
    ];

    public function getAvailabilityLabelAttribute(): string
    {
        return self::AVAILABILITY_STATUSES[$this->availability_status] ?? self::AVAILABILITY_STATUSES['sob_encomenda'];
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
