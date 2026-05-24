<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;

class Store extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'whatsapp',
        'instagram',
        'description',
        'logo',
        'banner',
        'primary_color',
        'secondary_color',
        'background_color',
        'text_color',
        'store_card_color',
        'store_muted_color',
        'store_border_color',
        'store_badge_color',
        'store_badge_text_color',
        'store_theme',
        'dashboard_theme',
        'dashboard_bg_color',
        'dashboard_card_color',
        'dashboard_primary_color',
        'dashboard_secondary_color',
        'dashboard_text_color',
        'dashboard_muted_color',
        'dashboard_border_color',
        'is_active',
        'onboarding_step',
        'onboarding_completed_at',
    ];

    protected $casts = [
        'onboarding_completed_at' => 'datetime',
    ];

    public function dashboardTheme(): array
    {
        if ($this->dashboard_theme === 'custom') {
            $fallback = config('dashboard-themes.blush');

            return [
                'name' => 'Personalizado',
                'bg' => $this->dashboard_bg_color ?: $fallback['bg'],
                'card' => $this->dashboard_card_color ?: $fallback['card'],
                'primary' => $this->dashboard_primary_color ?: $fallback['primary'],
                'secondary' => $this->dashboard_secondary_color ?: $fallback['secondary'],
                'text' => $this->dashboard_text_color ?: $fallback['text'],
                'muted' => $this->dashboard_muted_color ?: $fallback['muted'],
                'border' => $this->dashboard_border_color ?: $fallback['border'],
            ];
        }

        return config('dashboard-themes.' . ($this->dashboard_theme ?: 'blush'), config('dashboard-themes.blush'));
    }

    public function storefrontTheme(): array
    {
        $fallback = config('store-themes.candy');

        return [
            'primary' => $this->primary_color ?: $fallback['primary'],
            'secondary' => $this->secondary_color ?: $fallback['secondary'],
            'background' => $this->background_color ?: $fallback['background'],
            'text' => $this->text_color ?: $fallback['text'],
            'muted' => $this->store_muted_color ?: $fallback['muted'],
            'card' => $this->store_card_color ?: $fallback['card'],
            'border' => $this->store_border_color ?: $fallback['border'],
            'badge' => $this->store_badge_color ?: $fallback['badge'],
            'badge_text' => $this->store_badge_text_color ?: $fallback['badge_text'],
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
