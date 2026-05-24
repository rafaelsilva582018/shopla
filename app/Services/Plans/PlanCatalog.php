<?php

namespace App\Services\Plans;

use App\Models\SystemSetting;
use Illuminate\Database\QueryException;

class PlanCatalog
{
    public function all(): array
    {
        $plans = config('plans');
        $settings = $this->settings();
        $discount = $this->floatSetting($settings, 'plans.annual_discount_percent', (float) ($plans['plus']['annual_discount_percent'] ?? 10));
        $discount = max(0, min(90, $discount));
        $annualMultiplier = max(0, 1 - ($discount / 100));

        foreach (['free', 'plus', 'pro', 'premium'] as $planKey) {
            if (!isset($plans[$planKey])) {
                continue;
            }

            $plans[$planKey]['limit'] = $this->intSetting($settings, "plans.{$planKey}.limit", $plans[$planKey]['limit'] ?? null);
        }

        foreach (['plus', 'pro', 'premium'] as $planKey) {
            if (!isset($plans[$planKey]['price'])) {
                continue;
            }

            $price = $this->floatSetting($settings, "plans.{$planKey}.price", (float) $plans[$planKey]['price']);

            $plans[$planKey]['price'] = $price;
            $plans[$planKey]['annual_price'] = round($price * 12 * $annualMultiplier, 2);
            $plans[$planKey]['annual_discount_percent'] = $discount;
        }

        return $plans;
    }

    public function find(string $planKey): ?array
    {
        return $this->all()[$planKey] ?? null;
    }

    public function keys(): array
    {
        return array_keys($this->all());
    }

    public function settingsForForm(): array
    {
        $plans = $this->all();

        return [
            'plans.free.limit' => $plans['free']['limit'],
            'plans.plus.limit' => $plans['plus']['limit'],
            'plans.plus.price' => $plans['plus']['price'],
            'plans.pro.limit' => $plans['pro']['limit'],
            'plans.pro.price' => $plans['pro']['price'],
            'plans.premium.limit' => $plans['premium']['limit'],
            'plans.premium.price' => $plans['premium']['price'],
            'plans.annual_discount_percent' => $plans['plus']['annual_discount_percent'] ?? 10,
        ];
    }

    private function settings(): array
    {
        try {
            return SystemSetting::query()->pluck('value', 'key')->all();
        } catch (QueryException) {
            return [];
        }
    }

    private function intSetting(array $settings, string $key, ?int $default): ?int
    {
        $value = $settings[$key] ?? null;

        if ($value === null || $value === '') {
            return $default;
        }

        return max(0, (int) $value);
    }

    private function floatSetting(array $settings, string $key, float $default): float
    {
        $value = $settings[$key] ?? null;

        if ($value === null || $value === '' || !is_numeric($value)) {
            return $default;
        }

        return round(max(0, (float) $value), 2);
    }
}
