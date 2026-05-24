<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Support\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StoreController extends Controller
{
    public function create()
    {
        return view('store.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        if (Auth::user()->store) {
            return redirect('/dashboard')->with('error', 'Você já possui uma loja.');
        }

        Store::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'slug' => Str::slug($request->name . '-' . uniqid()),
            'whatsapp' => $request->whatsapp,
            'description' => $request->description,
            'dashboard_theme' => 'blush',
        ]);

        return redirect('/dashboard')->with('success', 'Loja criada com sucesso!');
    }

    public function edit()
    {
        $store = Auth::user()->store;

        return view('store.edit', compact('store'));
    }

    public function update(Request $request)
    {
        $store = Auth::user()->store;
        $canChooseCustomSlug = Auth::user()->canChooseCustomSlug();

        $rules = [
            'name' => 'required|string|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'instagram' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:51200',
            'banner' => 'nullable|image|max:51200',
            'store_theme_mode' => ['required', Rule::in(['preset', 'custom'])],
            'store_theme' => ['required_if:store_theme_mode,preset', 'nullable', Rule::in(array_keys(config('store-themes')))],
            'primary_color' => 'required_if:store_theme_mode,custom|string|max:20',
            'secondary_color' => 'required_if:store_theme_mode,custom|string|max:20',
            'background_color' => 'required_if:store_theme_mode,custom|string|max:20',
            'text_color' => 'required_if:store_theme_mode,custom|string|max:20',
            'store_card_color' => 'required_if:store_theme_mode,custom|string|max:20',
            'store_muted_color' => 'required_if:store_theme_mode,custom|string|max:20',
            'store_border_color' => 'required_if:store_theme_mode,custom|string|max:20',
            'store_badge_color' => 'required_if:store_theme_mode,custom|string|max:20',
            'store_badge_text_color' => 'required_if:store_theme_mode,custom|string|max:20',
            'dashboard_theme_mode' => ['required', Rule::in(['preset', 'custom'])],
            'dashboard_theme' => ['required_if:dashboard_theme_mode,preset', 'nullable', Rule::in(array_keys(config('dashboard-themes')))],
            'dashboard_bg_color' => 'required_if:dashboard_theme_mode,custom|string|max:20',
            'dashboard_card_color' => 'required_if:dashboard_theme_mode,custom|string|max:20',
            'dashboard_primary_color' => 'required_if:dashboard_theme_mode,custom|string|max:20',
            'dashboard_secondary_color' => 'required_if:dashboard_theme_mode,custom|string|max:20',
            'dashboard_text_color' => 'required_if:dashboard_theme_mode,custom|string|max:20',
            'dashboard_muted_color' => 'required_if:dashboard_theme_mode,custom|string|max:20',
            'dashboard_border_color' => 'required_if:dashboard_theme_mode,custom|string|max:20',
            'remove_logo' => 'nullable|boolean',
            'remove_banner' => 'nullable|boolean',
        ];

        if ($canChooseCustomSlug) {
            $request->merge([
                'slug' => Str::slug($request->input('slug')),
            ]);

            $rules['slug'] = [
                'required',
                'string',
                'max:255',
                'unique:stores,slug,' . $store->id,
                fn (string $attribute, mixed $value, \Closure $fail) => $this->validateSlugCanBeUsed($value, $fail),
            ];
        }

        $request->validate($rules);

        $data = $request->only([
            'name',
            'whatsapp',
            'instagram',
            'description',
            'store_theme',
            'primary_color',
            'secondary_color',
            'background_color',
            'text_color',
            'store_card_color',
            'store_muted_color',
            'store_border_color',
            'store_badge_color',
            'store_badge_text_color',
            'dashboard_theme',
            'dashboard_bg_color',
            'dashboard_card_color',
            'dashboard_primary_color',
            'dashboard_secondary_color',
            'dashboard_text_color',
            'dashboard_muted_color',
            'dashboard_border_color',
        ]);

        if ($canChooseCustomSlug) {
            $data['slug'] = Str::slug($request->input('slug'));
        }

        $data['instagram'] = $this->normalizeInstagram($data['instagram'] ?? null);

        if ($request->store_theme_mode === 'preset') {
            $selectedTheme = config('store-themes.' . $request->store_theme);

            $data['store_theme'] = $request->store_theme;
            $data['primary_color'] = $selectedTheme['primary'];
            $data['secondary_color'] = $selectedTheme['secondary'];
            $data['background_color'] = $selectedTheme['background'];
            $data['text_color'] = $selectedTheme['text'];
            $data['store_card_color'] = $selectedTheme['card'];
            $data['store_muted_color'] = $selectedTheme['muted'];
            $data['store_border_color'] = $selectedTheme['border'];
            $data['store_badge_color'] = $selectedTheme['badge'];
            $data['store_badge_text_color'] = $selectedTheme['badge_text'];
        } else {
            $data['store_theme'] = 'custom';
        }

        if ($request->dashboard_theme_mode === 'preset') {
            $data['dashboard_theme'] = $request->dashboard_theme;
            $data['dashboard_bg_color'] = null;
            $data['dashboard_card_color'] = null;
            $data['dashboard_primary_color'] = null;
            $data['dashboard_secondary_color'] = null;
            $data['dashboard_text_color'] = null;
            $data['dashboard_muted_color'] = null;
            $data['dashboard_border_color'] = null;
        } else {
            $data['dashboard_theme'] = 'custom';
        }

        // Remover logo atual
        if ($request->boolean('remove_logo') && $store->logo) {
            Storage::disk('public')->delete($store->logo);
            $data['logo'] = null;
        }

        // Remover banner atual
        if ($request->boolean('remove_banner') && $store->banner) {
            Storage::disk('public')->delete($store->banner);
            $data['banner'] = null;
        }

        // Enviar nova logo
        if ($request->hasFile('logo')) {
            if ($store->logo) {
                Storage::disk('public')->delete($store->logo);
            }

            $data['logo'] = ImageOptimizer::store($request->file('logo'), 'stores/logos', 800, 800, 86);
        }

        // Enviar novo banner
        if ($request->hasFile('banner')) {
            if ($store->banner) {
                Storage::disk('public')->delete($store->banner);
            }

            $data['banner'] = ImageOptimizer::store($request->file('banner'), 'stores/banners', 2200, 1000, 84);
        }

        $store->update($data);

        return back()->with('success', 'Configurações salvas com sucesso!');
    }

    private function normalizeInstagram(?string $instagram): ?string
    {
        if (!$instagram) {
            return null;
        }

        $instagram = trim($instagram);
        $instagram = ltrim($instagram, '@');

        return $instagram ?: null;
    }

    private function isReservedSlug(?string $slug): bool
    {
        return in_array(Str::slug($slug ?? ''), [
            'admin',
            'api',
            'dashboard',
            'entrar',
            'financeiro',
            'login',
            'logout',
            'minha-loja',
            'onboarding',
            'pedidos',
            'perfil',
            'planos',
            'profile',
            'register',
            'registro',
            'senha',
            'storage',
            'webhooks',
        ], true);
    }

    private function validateSlugCanBeUsed(mixed $slug, \Closure $fail): void
    {
        if ($this->isReservedSlug((string) $slug)) {
            $fail('Esse link e reservado pelo sistema. Escolha outro.');
        }
    }
}
