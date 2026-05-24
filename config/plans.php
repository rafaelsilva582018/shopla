<?php

$plusPrice = (float) env('PLAN_PLUS_PRICE', 6.99);
$proPrice = (float) env('PLAN_PRO_PRICE', 12.99);
$premiumPrice = (float) env('PLAN_PREMIUM_PRICE', 22.99);
$annualDiscountPercent = (float) env('PLAN_ANNUAL_DISCOUNT_PERCENT', 10);
$annualDiscountMultiplier = max(0, 1 - ($annualDiscountPercent / 100));
$annualPrice = fn (float $price): float => round($price * 12 * $annualDiscountMultiplier, 2);

return [
    'free' => [
        'name' => 'Gratuito',
        'limit' => 6,
        'custom_slug' => false,
        'description' => 'Para comecar a montar a vitrine.',
    ],
    'plus' => [
        'name' => 'Plus',
        'limit' => 50,
        'price' => $plusPrice,
        'annual_price' => $annualPrice($plusPrice),
        'annual_discount_percent' => $annualDiscountPercent,
        'cycle' => env('PLAN_PLUS_CYCLE', 'MONTHLY'),
        'annual_cycle' => env('PLAN_PLUS_ANNUAL_CYCLE', 'YEARLY'),
        'period_label' => 'por mes',
        'annual_label' => 'por ano',
        'billing_label' => 'mensal',
        'annual_billing_label' => 'anual',
        'custom_slug' => true,
        'description' => 'Para lojas pequenas que querem sair do improviso.',
    ],
    'pro' => [
        'name' => 'Pro',
        'limit' => 100,
        'price' => $proPrice,
        'annual_price' => $annualPrice($proPrice),
        'annual_discount_percent' => $annualDiscountPercent,
        'cycle' => env('PLAN_PRO_CYCLE', 'MONTHLY'),
        'annual_cycle' => env('PLAN_PRO_ANNUAL_CYCLE', 'YEARLY'),
        'period_label' => 'por mes',
        'annual_label' => 'por ano',
        'billing_label' => 'mensal',
        'annual_billing_label' => 'anual',
        'custom_slug' => true,
        'description' => 'Para lojas com catalogo crescendo e mais pedidos.',
    ],
    'premium' => [
        'name' => 'Premium',
        'limit' => 200,
        'price' => $premiumPrice,
        'annual_price' => $annualPrice($premiumPrice),
        'annual_discount_percent' => $annualDiscountPercent,
        'cycle' => env('PLAN_PREMIUM_CYCLE', 'MONTHLY'),
        'annual_cycle' => env('PLAN_PREMIUM_ANNUAL_CYCLE', 'YEARLY'),
        'period_label' => 'por mes',
        'annual_label' => 'por ano',
        'billing_label' => 'mensal',
        'annual_billing_label' => 'anual',
        'custom_slug' => true,
        'description' => 'Para lojas com catalogo grande e operacao mais intensa.',
    ],
    'enterprise' => [
        'name' => 'Sob consulta',
        'limit' => null,
        'custom_slug' => true,
        'description' => 'Para lojas que precisam de mais de 200 produtos.',
    ],
];
