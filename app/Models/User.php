<?php

namespace App\Models;

use App\Notifications\VerifyEmailNotification;
use App\Services\Plans\PlanCatalog;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'last_name',
    'email',
    'email_verified_at',
    'google_id',
    'google_avatar',
    'phone',
    'birthdate',
    'document',
    'city',
    'state',
    'zip_code',
    'address',
    'address_number',
    'address_complement',
    'district',
    'plan',
    'plan_started_at',
    'dashboard_theme',
    'onboarding_plan',
    'password',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification);
    }

    public function store()
    {
        return $this->hasOne(Store::class);
    }

    public function planSubscriptions()
    {
        return $this->hasMany(PlanSubscription::class);
    }

    public function activePlanSubscription()
    {
        return $this->hasOne(PlanSubscription::class)->where('status', 'active')->latestOfMany();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birthdate' => 'date',
            'plan_started_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function planConfig(): array
    {
        return app(PlanCatalog::class)->find($this->plan ?: 'free')
            ?: app(PlanCatalog::class)->find('free');
    }

    public function planName(): string
    {
        return $this->planConfig()['name'];
    }

    public function productLimit(): ?int
    {
        return $this->planConfig()['limit'];
    }

    public function productLimitLabel(): string
    {
        return $this->productLimit() ? (string) $this->productLimit() : 'Ilimitado';
    }

    public function canChooseCustomSlug(): bool
    {
        return (bool) ($this->planConfig()['custom_slug'] ?? false);
    }

    public function isAdmin(): bool
    {
        return in_array(strtolower($this->email), config('admin.emails', []), true);
    }

    public function canCreateProductForStore(): bool
    {
        $limit = $this->productLimit();

        if ($limit === null) {
            return true;
        }

        return ($this->store?->products()->count() ?? 0) < $limit;
    }
}
