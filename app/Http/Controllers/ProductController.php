<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $store = $user->store;

        $statusFilter = request('status', 'todos');

        $productsQuery = $store->products()
            ->with(['category', 'categories'])
            ->latest();

        if ($statusFilter === 'ativos') {
            $productsQuery->where('is_active', true);
        }

        if ($statusFilter === 'inativos') {
            $productsQuery->where('is_active', false);
        }

        if ($statusFilter === 'destaques') {
            $productsQuery->where('is_featured', true);
        }

        if ($statusFilter === 'sem-categoria') {
            $productsQuery->whereDoesntHave('categories');
        }

        $products = $productsQuery->get();

        $productLimit = $user->productLimit();
        $canCreateProduct = $user->canCreateProductForStore();

        return view('products.index', compact('products', 'statusFilter', 'productLimit', 'canCreateProduct'));
    }

    public function create()
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$user->canCreateProductForStore()) {
            return redirect()
                ->to(route('products.index', absolute: false))
                ->with('error', 'Seu plano chegou ao limite de produtos. Atualize o plano para cadastrar mais itens.');
        }

        $categories = $store->categories()->orderBy('name')->get();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$user->canCreateProductForStore()) {
            return back()
                ->withInput()
                ->with('error', 'Seu plano chegou ao limite de produtos. Atualize o plano para cadastrar mais itens.');
        }

        $request->merge([
            'price' => $this->normalizePrice($request->input('price')),
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where('store_id', $store->id),
            ],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => [
                Rule::exists('categories', 'id')->where('store_id', $store->id),
            ],
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:51200',
            'image_2' => 'nullable|image|max:51200',
            'image_3' => 'nullable|image|max:51200',
            'image_position' => ['nullable', 'regex:/^\d{1,3}% \d{1,3}%$|^(top|center|bottom)$/'],
            'image_2_position' => ['nullable', 'regex:/^\d{1,3}% \d{1,3}%$|^(top|center|bottom)$/'],
            'image_3_position' => ['nullable', 'regex:/^\d{1,3}% \d{1,3}%$|^(top|center|bottom)$/'],
            'availability_status' => ['required', Rule::in(array_keys(Product::AVAILABILITY_STATUSES))],
            'stock_quantity' => 'nullable|integer|min:0',
        ], $this->productValidationMessages());

        $trackStock = $request->availability_status === 'esgotado' || $request->filled('stock_quantity');

        $imagePath = null;
        $secondImagePath = null;
        $thirdImagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = ImageOptimizer::store($request->file('image'), 'products');
        }

        if ($request->hasFile('image_2')) {
            $secondImagePath = ImageOptimizer::store($request->file('image_2'), 'products');
        }

        if ($request->hasFile('image_3')) {
            $thirdImagePath = ImageOptimizer::store($request->file('image_3'), 'products');
        }

        $categoryIds = $this->selectedCategoryIds($request);

        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $categoryIds[0] ?? null,
            'name' => $request->name,
            'slug' => Str::slug($request->name . '-' . uniqid()),
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
            'image_2' => $secondImagePath,
            'image_3' => $thirdImagePath,
            'image_position' => $this->normalizeImagePosition($request->input('image_position')),
            'image_2_position' => $this->normalizeImagePosition($request->input('image_2_position')),
            'image_3_position' => $this->normalizeImagePosition($request->input('image_3_position')),
            'availability_status' => $request->availability_status,
            'stock_quantity' => $request->availability_status === 'esgotado' ? 0 : (int) $request->input('stock_quantity', 0),
            'track_stock' => $trackStock,
            'is_active' => $request->boolean('is_active', true),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        $product->categories()->sync($categoryIds);

        return redirect()->to(route('products.index', absolute: false))->with('success', 'Produto criado com sucesso!');
    }

    public function destroy(Product $product)
    {
        $store = Auth::user()->store;

        abort_if($product->store_id !== $store->id, 403);

        $product->delete();

        return back()->with('success', 'Produto removido!');
    }
    public function edit(Product $product)
    {
        $store = Auth::user()->store;

        abort_if($product->store_id !== $store->id, 403);

        $categories = $store->categories()->orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $store = Auth::user()->store;

        abort_if($product->store_id !== $store->id, 403);

        $request->merge([
            'price' => $this->normalizePrice($request->input('price')),
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where('store_id', $store->id),
            ],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => [
                Rule::exists('categories', 'id')->where('store_id', $store->id),
            ],
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:51200',
            'image_2' => 'nullable|image|max:51200',
            'image_3' => 'nullable|image|max:51200',
            'image_position' => ['nullable', 'regex:/^\d{1,3}% \d{1,3}%$|^(top|center|bottom)$/'],
            'image_2_position' => ['nullable', 'regex:/^\d{1,3}% \d{1,3}%$|^(top|center|bottom)$/'],
            'image_3_position' => ['nullable', 'regex:/^\d{1,3}% \d{1,3}%$|^(top|center|bottom)$/'],
            'image_order' => ['nullable', 'string', 'max:50'],
            'availability_status' => ['required', Rule::in(array_keys(Product::AVAILABILITY_STATUSES))],
            'stock_quantity' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ], $this->productValidationMessages());

        $trackStock = $request->availability_status === 'esgotado' || $request->filled('stock_quantity');
        $categoryIds = $this->selectedCategoryIds($request);

        $data = $request->only([
            'name',
            'description',
            'price',
            'availability_status',
            'stock_quantity',
            'image_position',
            'image_2_position',
            'image_3_position',
        ]);

        $data['category_id'] = $categoryIds[0] ?? null;

        $data = array_merge($this->reorderExistingImages($request, $product), $data);

        $data['image_position'] = $this->normalizeImagePosition($request->input('image_position'));
        $data['image_2_position'] = $this->normalizeImagePosition($request->input('image_2_position'));
        $data['image_3_position'] = $this->normalizeImagePosition($request->input('image_3_position'));

        $data['slug'] = Str::slug($request->name . '-' . $product->id);
        $data['stock_quantity'] = $request->availability_status === 'esgotado'
            ? 0
            : (int) $request->input('stock_quantity', 0);
        $data['track_stock'] = $trackStock;
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            $this->deleteImageIfNotReused($product->image, $data);

            $data['image'] = ImageOptimizer::store($request->file('image'), 'products');
        }

        if ($request->hasFile('image_2')) {
            $this->deleteImageIfNotReused($product->image_2, $data);

            $data['image_2'] = ImageOptimizer::store($request->file('image_2'), 'products');
        }

        if ($request->hasFile('image_3')) {
            $this->deleteImageIfNotReused($product->image_3, $data);

            $data['image_3'] = ImageOptimizer::store($request->file('image_3'), 'products');
        }

        $product->update($data);
        $product->categories()->sync($categoryIds);

        return redirect()->to(route('products.index', absolute: false))->with('success', 'Produto atualizado com sucesso!');
    }

    private function selectedCategoryIds(Request $request): array
    {
        $ids = collect($request->input('category_ids', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty() && $request->filled('category_id')) {
            $ids->push((int) $request->input('category_id'));
        }

        return $ids->all();
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

    private function normalizeImagePosition(?string $position): string
    {
        return match ($position) {
            'top' => '50% 0%',
            'bottom' => '50% 100%',
            'center', null, '' => '50% 50%',
            default => preg_match('/^\d{1,3}% \d{1,3}%$/', $position)
                ? $this->clampImagePosition($position)
                : '50% 50%',
        };
    }

    private function clampImagePosition(string $position): string
    {
        [$x, $y] = array_map(
            fn (string $value) => max(0, min(100, (int) rtrim($value, '%'))),
            explode(' ', $position)
        );

        return "{$x}% {$y}%";
    }

    private function reorderExistingImages(Request $request, Product $product): array
    {
        $slots = ['image', 'image_2', 'image_3'];
        $order = collect(explode(',', (string) $request->input('image_order')))
            ->filter(fn (string $slot) => in_array($slot, $slots, true))
            ->values();

        if ($order->count() !== 3) {
            return [];
        }

        $current = [
            'image' => $product->image,
            'image_2' => $product->image_2,
            'image_3' => $product->image_3,
        ];

        return collect($slots)
            ->mapWithKeys(fn (string $slot, int $index) => [$slot => $current[$order[$index]] ?? null])
            ->all();
    }

    private function deleteImageIfNotReused(?string $path, array $data): void
    {
        if (!$path) {
            return;
        }

        $reusedPaths = [
            $data['image'] ?? null,
            $data['image_2'] ?? null,
            $data['image_3'] ?? null,
        ];

        if (!in_array($path, $reusedPaths, true)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function productValidationMessages(): array
    {
        return [
            'image.uploaded' => 'A imagem principal não chegou ao servidor. Tente novamente ou escolha uma foto menor.',
            'image.image' => 'A imagem principal precisa ser um arquivo de imagem.',
            'image.max' => 'A imagem principal pode ter no máximo 50 MB antes da otimização.',
            'image_2.uploaded' => 'A segunda imagem não chegou ao servidor. Tente novamente ou escolha uma foto menor.',
            'image_2.image' => 'A segunda imagem precisa ser um arquivo de imagem.',
            'image_2.max' => 'A segunda imagem pode ter no máximo 50 MB antes da otimização.',
            'image_3.uploaded' => 'A terceira imagem não chegou ao servidor. Tente novamente ou escolha uma foto menor.',
            'image_3.image' => 'A terceira imagem precisa ser um arquivo de imagem.',
            'image_3.max' => 'A terceira imagem pode ter no máximo 50 MB antes da otimização.',
            'price.numeric' => 'Informe um preço válido, como 19,90.',
        ];
    }
}
