<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\Plans\PlanCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    public function index(PlanCatalog $planCatalog): View
    {
        $plans = $planCatalog->all();
        $settings = $planCatalog->settingsForForm();

        return view('admin.settings.index', compact('plans', 'settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'free_limit' => ['required', 'integer', 'min:0', 'max:1000'],
            'plus_limit' => ['required', 'integer', 'min:1', 'max:1000'],
            'plus_price' => ['required', 'numeric', 'min:0', 'max:9999'],
            'pro_limit' => ['required', 'integer', 'min:1', 'max:1000'],
            'pro_price' => ['required', 'numeric', 'min:0', 'max:9999'],
            'premium_limit' => ['required', 'integer', 'min:1', 'max:1000'],
            'premium_price' => ['required', 'numeric', 'min:0', 'max:9999'],
            'annual_discount_percent' => ['required', 'numeric', 'min:0', 'max:90'],
        ]);

        SystemSetting::setMany([
            'plans.free.limit' => $data['free_limit'],
            'plans.plus.limit' => $data['plus_limit'],
            'plans.plus.price' => $this->normalizeMoney($data['plus_price']),
            'plans.pro.limit' => $data['pro_limit'],
            'plans.pro.price' => $this->normalizeMoney($data['pro_price']),
            'plans.premium.limit' => $data['premium_limit'],
            'plans.premium.price' => $this->normalizeMoney($data['premium_price']),
            'plans.annual_discount_percent' => $this->normalizeMoney($data['annual_discount_percent']),
        ]);

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Configuracoes globais atualizadas.');
    }

    private function normalizeMoney(mixed $value): string
    {
        return number_format((float) $value, 2, '.', '');
    }
}
