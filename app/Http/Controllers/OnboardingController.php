<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Services\Billing\PlanCheckoutService;
use App\Services\Plans\PlanCatalog;
use App\Support\ImageOptimizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RuntimeException;

class OnboardingController extends Controller
{
    public function index(PlanCatalog $planCatalog)
    {
        $user = Auth::user();
        $store = $user->store;
        $categories = $store ? $store->categories()->orderBy('name')->get() : collect();
        $plans = $planCatalog->all();
        $latestSubscription = $user->planSubscriptions()->latest()->first();
        $asaasReady = filled(config('services.asaas.access_token'));
        $paidPlanKeys = ['plus', 'pro', 'premium'];
        $hasPendingPaidPlan = $latestSubscription
            && $latestSubscription->status === 'pending'
            && in_array($latestSubscription->plan, $paidPlanKeys, true)
            && ($user->plan ?: 'free') !== $latestSubscription->plan;
        $selectedOnboardingPlan = old('plan', $hasPendingPaidPlan
            ? $latestSubscription->plan
            : ($user->onboarding_plan ?: ($user->plan !== 'free' ? $user->plan : 'free')));
        $step = $this->currentStep($user, $store, $hasPendingPaidPlan);
        $canChooseCustomSlug = $user->canChooseCustomSlug();

        if ($store?->onboarding_completed_at) {
            return redirect()->route('dashboard');
        }

        return view('onboarding.index', compact(
            'user',
            'store',
            'step',
            'categories',
            'plans',
            'latestSubscription',
            'asaasReady',
            'canChooseCustomSlug',
            'selectedOnboardingPlan',
        ));
    }

    public function panel(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'dashboard_theme' => ['required', Rule::in(array_keys(config('dashboard-themes')))],
        ]);

        $user->update([
            'dashboard_theme' => $data['dashboard_theme'],
        ]);

        return redirect()->route('onboarding.index');
    }

    public function plan(Request $request, PlanCatalog $planCatalog, PlanCheckoutService $checkout)
    {
        $user = Auth::user();
        $planKeys = collect($planCatalog->keys())
            ->reject(fn (string $plan) => $plan === 'enterprise')
            ->values()
            ->all();

        $data = $request->validate([
            'plan' => ['required', Rule::in($planKeys)],
            'billing_period' => ['nullable', 'in:monthly,annual'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required_unless:plan,free', 'nullable', 'string', 'max:30'],
            'document' => ['required_unless:plan,free', 'nullable', 'string', 'max:30'],
            'zip_code' => ['required_unless:plan,free', 'nullable', 'string', 'max:20'],
            'address' => ['required_unless:plan,free', 'nullable', 'string', 'max:255'],
            'address_number' => ['required_unless:plan,free', 'nullable', 'string', 'max:30'],
            'address_complement' => ['nullable', 'string', 'max:255'],
            'district' => ['required_unless:plan,free', 'nullable', 'string', 'max:120'],
            'city' => ['required_unless:plan,free', 'nullable', 'string', 'max:120'],
            'state' => ['required_unless:plan,free', 'nullable', 'string', 'size:2'],
        ], [
            'phone.required_unless' => 'Informe o WhatsApp para liberar o checkout.',
            'document.required_unless' => 'Informe CPF ou CNPJ para liberar o checkout.',
            'zip_code.required_unless' => 'Informe o CEP para liberar o checkout.',
            'address.required_unless' => 'Informe a rua para liberar o checkout.',
            'address_number.required_unless' => 'Informe o numero para liberar o checkout.',
            'district.required_unless' => 'Informe o bairro para liberar o checkout.',
            'city.required_unless' => 'Informe a cidade para liberar o checkout.',
            'state.required_unless' => 'Informe a UF para liberar o checkout.',
        ]);

        if ($data['plan'] === 'free') {
            $user->update([
                'plan' => 'free',
                'plan_started_at' => $user->plan_started_at ?: now(),
                'onboarding_plan' => 'free',
            ]);

            return redirect()->route('onboarding.index');
        }

        if (!filled(config('services.asaas.access_token'))) {
            return back()->with('error', 'O pagamento ainda nao foi configurado. Tente o plano gratuito por enquanto.');
        }

        $user->update([
            'last_name' => $data['last_name'] ?? $user->last_name,
            'phone' => $data['phone'] ?? null,
            'document' => $data['document'] ?? null,
            'zip_code' => $data['zip_code'] ?? null,
            'address' => $data['address'] ?? null,
            'address_number' => $data['address_number'] ?? null,
            'address_complement' => $data['address_complement'] ?? null,
            'district' => $data['district'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => isset($data['state']) ? strtoupper($data['state']) : null,
            'onboarding_plan' => $data['plan'],
        ]);

        try {
            $subscription = $checkout->start($user->fresh(), $data['plan'], 'onboarding', $data['billing_period'] ?? 'monthly');
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        if (!$subscription->checkout_url) {
            return back()->withInput()->with('error', 'O Asaas nao retornou o link de pagamento. Tente novamente.');
        }

        return redirect()->away($subscription->checkout_url);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->store) {
            return redirect()->route('onboarding.index');
        }

        $canChooseCustomSlug = $user->canChooseCustomSlug();
        $requestedSlug = Str::slug($request->input('slug'));

        if (!$canChooseCustomSlug || blank($requestedSlug)) {
            $request->request->remove('slug');
        } else {
            $request->merge(['slug' => $requestedSlug]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                Rule::requiredIf($canChooseCustomSlug && filled($requestedSlug)),
                'nullable',
                'string',
                'max:255',
                'unique:stores,slug',
                fn (string $attribute, mixed $value, \Closure $fail) => $this->validateSlugCanBeUsed($value, $fail),
            ],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'instagram' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
            'store_theme' => ['required', Rule::in(array_keys(config('store-themes')))],
        ]);

        $storeTheme = config('store-themes.' . $data['store_theme']);

        Store::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'slug' => $canChooseCustomSlug && filled($data['slug'] ?? null)
                ? Str::slug($data['slug'])
                : $this->generateFreeStoreSlug(),
            'whatsapp' => $data['whatsapp'] ?? null,
            'instagram' => $this->normalizeInstagram($data['instagram'] ?? null),
            'description' => $data['description'] ?? null,
            'store_theme' => $data['store_theme'],
            'primary_color' => $storeTheme['primary'],
            'secondary_color' => $storeTheme['secondary'],
            'background_color' => $storeTheme['background'],
            'text_color' => $storeTheme['text'],
            'store_card_color' => $storeTheme['card'],
            'store_muted_color' => $storeTheme['muted'],
            'store_border_color' => $storeTheme['border'],
            'store_badge_color' => $storeTheme['badge'],
            'store_badge_text_color' => $storeTheme['badge_text'],
            'dashboard_theme' => $user->dashboard_theme ?: 'blush',
            'onboarding_step' => 4,
        ]);

        return redirect()->route('onboarding.index');
    }

    public function checkSlug(Request $request): JsonResponse
    {
        $slug = Str::slug($request->query('slug', ''));
        $ignoreStoreId = $request->user()->store?->id;

        $available = filled($slug)
            && !$this->isReservedSlug($slug)
            && !Store::where('slug', $slug)
                ->when($ignoreStoreId, fn ($query) => $query->whereKeyNot($ignoreStoreId))
                ->exists();

        return response()->json([
            'slug' => $slug,
            'available' => $available,
            'message' => match (true) {
                blank($slug) => 'Digite um link para verificar.',
                $this->isReservedSlug($slug) => 'Esse link e reservado pelo sistema.',
                $available => 'Link disponivel.',
                default => 'Esse link ja esta em uso.',
            },
        ]);
    }

    public function categories(Request $request)
    {
        $store = Auth::user()->store;

        abort_if(!$store, 404);

        $data = $request->validate([
            'categories' => ['nullable', 'array'],
            'categories.*' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($data['categories'] ?? [] as $name) {
            $name = trim($name);

            if (!$name) {
                continue;
            }

            Category::firstOrCreate([
                'store_id' => $store->id,
                'slug' => Str::slug($name),
            ], [
                'name' => $name,
            ]);
        }

        $store->update(['onboarding_step' => 5]);

        return redirect()->route('onboarding.index');
    }

    public function skipCategories()
    {
        $store = Auth::user()->store;

        abort_if(!$store, 404);

        $store->update(['onboarding_step' => 5]);

        return redirect()->route('onboarding.index');
    }

    public function product(Request $request)
    {
        $user = Auth::user();
        $store = $user->store;

        abort_if(!$store, 404);

        if (!$user->canCreateProductForStore()) {
            return redirect()->route('onboarding.index')->with('error', 'Seu plano chegou ao limite de produtos.');
        }

        $request->merge([
            'price' => $this->normalizePrice($request->input('price')),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', Rule::exists('categories', 'id')->where('store_id', $store->id)],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => [Rule::exists('categories', 'id')->where('store_id', $store->id)],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:51200'],
            'availability_status' => ['required', Rule::in(array_keys(Product::AVAILABILITY_STATUSES))],
        ], [
            'image.uploaded' => 'A imagem não chegou ao servidor. Tente novamente ou escolha uma foto menor.',
            'image.image' => 'Envie um arquivo de imagem válido.',
            'image.max' => 'A imagem pode ter no máximo 50 MB antes da otimização.',
            'price.numeric' => 'Informe um preço válido, como 19,90.',
        ]);

        $imagePath = $request->hasFile('image')
            ? ImageOptimizer::store($request->file('image'), 'products')
            : null;

        $categoryIds = collect($request->input('category_ids', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($categoryIds->isEmpty() && !empty($data['category_id'])) {
            $categoryIds->push((int) $data['category_id']);
        }

        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $categoryIds->first(),
            'name' => $data['name'],
            'slug' => Str::slug($data['name'] . '-' . uniqid()),
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'image' => $imagePath,
            'availability_status' => $data['availability_status'],
            'stock_quantity' => 0,
            'track_stock' => false,
            'is_active' => true,
        ]);

        $product->categories()->sync($categoryIds->all());

        $store->update([
            'onboarding_step' => 6,
            'onboarding_completed_at' => now(),
        ]);

        return redirect()->route('onboarding.index');
    }

    private function normalizePrice(mixed $price): mixed
    {
        if (!is_string($price)) {
            return $price;
        }

        $price = trim($price);

        if (str_contains($price, ',') && str_contains($price, '.')) {
            return str_replace(',', '.', str_replace('.', '', $price));
        }

        if (str_contains($price, ',')) {
            return str_replace(',', '.', $price);
        }

        return $price;
    }

    public function skipProduct()
    {
        $store = Auth::user()->store;

        abort_if(!$store, 404);

        $store->update([
            'onboarding_step' => 6,
            'onboarding_completed_at' => now(),
        ]);

        return redirect()->route('onboarding.index');
    }

    public function finish()
    {
        $store = Auth::user()->store;

        abort_if(!$store, 404);

        $store->update([
            'onboarding_step' => 6,
            'onboarding_completed_at' => $store->onboarding_completed_at ?: now(),
        ]);

        return redirect()->route('dashboard');
    }

    private function normalizeInstagram(?string $instagram): ?string
    {
        if (!$instagram) {
            return null;
        }

        return ltrim(trim($instagram), '@') ?: null;
    }

    private function currentStep($user, ?Store $store, bool $hasPendingPaidPlan): int
    {
        if (!$user->dashboard_theme) {
            return 1;
        }

        if (!$store && ($hasPendingPaidPlan || (!$user->onboarding_plan && ($user->plan ?: 'free') === 'free'))) {
            return 2;
        }

        if (!$store) {
            return 3;
        }

        return $store->onboarding_completed_at ? 6 : (int) ($store->onboarding_step ?: 4);
    }

    private function generateFreeStoreSlug(): string
    {
        do {
            $slug = 'loja-' . Str::lower(Str::random(6));
        } while ($this->isReservedSlug($slug) || Store::where('slug', $slug)->exists());

        return $slug;
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
