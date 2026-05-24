<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\Plans\PlanCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request, PlanCatalog $planCatalog): View
    {
        $user = $request->user();
        $store = $user->store;

        return view('profile.edit', [
            'user' => $user,
            'store' => $store,
            'plans' => $planCatalog->all(),
            'latestSubscription' => $user->planSubscriptions()->latest()->first(),
            'activeSubscription' => $user->activePlanSubscription,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['state'] = isset($data['state']) ? strtoupper($data['state']) : null;
        $data['phone'] = $data['phone'] ?? null;
        $data['document'] = $data['document'] ?? null;
        $data['zip_code'] = $data['zip_code'] ?? null;

        $request->user()->fill($data);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updateDashboardTheme(Request $request): RedirectResponse
    {
        $store = $request->user()->store;

        abort_if(!$store, 404);

        $data = $request->validate([
            'dashboard_theme_mode' => ['required', Rule::in(['preset', 'custom'])],
            'dashboard_theme' => ['required_if:dashboard_theme_mode,preset', 'nullable', Rule::in(array_keys(config('dashboard-themes')))],
            'dashboard_bg_color' => ['required_if:dashboard_theme_mode,custom', 'nullable', 'string', 'max:20'],
            'dashboard_card_color' => ['required_if:dashboard_theme_mode,custom', 'nullable', 'string', 'max:20'],
            'dashboard_primary_color' => ['required_if:dashboard_theme_mode,custom', 'nullable', 'string', 'max:20'],
            'dashboard_secondary_color' => ['required_if:dashboard_theme_mode,custom', 'nullable', 'string', 'max:20'],
            'dashboard_text_color' => ['required_if:dashboard_theme_mode,custom', 'nullable', 'string', 'max:20'],
            'dashboard_muted_color' => ['required_if:dashboard_theme_mode,custom', 'nullable', 'string', 'max:20'],
            'dashboard_border_color' => ['required_if:dashboard_theme_mode,custom', 'nullable', 'string', 'max:20'],
        ]);

        if ($data['dashboard_theme_mode'] === 'preset') {
            $store->update([
                'dashboard_theme' => $data['dashboard_theme'],
                'dashboard_bg_color' => null,
                'dashboard_card_color' => null,
                'dashboard_primary_color' => null,
                'dashboard_secondary_color' => null,
                'dashboard_text_color' => null,
                'dashboard_muted_color' => null,
                'dashboard_border_color' => null,
            ]);
        } else {
            $store->update([
                'dashboard_theme' => 'custom',
                'dashboard_bg_color' => $data['dashboard_bg_color'],
                'dashboard_card_color' => $data['dashboard_card_color'],
                'dashboard_primary_color' => $data['dashboard_primary_color'],
                'dashboard_secondary_color' => $data['dashboard_secondary_color'],
                'dashboard_text_color' => $data['dashboard_text_color'],
                'dashboard_muted_color' => $data['dashboard_muted_color'],
                'dashboard_border_color' => $data['dashboard_border_color'],
            ]);
        }

        return Redirect::route('profile.edit')->with('status', 'dashboard-theme-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
