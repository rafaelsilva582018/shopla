<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoStoresSeeder extends Seeder
{
    public function run(): void
    {
        $password = (string) (config('demo.stores_password') ?: Str::password(16));
        $credentials = [];

        DB::transaction(function () use ($password, &$credentials) {
            foreach ($this->stores() as $index => $definition) {
                $email = 'demo' . ($index + 1) . '@shopla.test';
                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $definition['owner'],
                        'last_name' => 'Loja Exemplo',
                        'email_verified_at' => now(),
                        'password' => Hash::make($password),
                        'phone' => '11999999999',
                        'city' => 'Sao Paulo',
                        'state' => 'SP',
                        'zip_code' => '01001000',
                        'address' => 'Praca da Se',
                        'address_number' => (string) ($index + 10),
                        'district' => 'Centro',
                        'plan' => 'free',
                        'plan_started_at' => now(),
                        'onboarding_plan' => 'free',
                    ]
                );

                $store = Store::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name' => $definition['name'],
                        'slug' => $definition['slug'],
                        'whatsapp' => '5511999999999',
                        'instagram' => '@' . str_replace('-', '', $definition['slug']),
                        'description' => $definition['description'],
                        'primary_color' => $definition['colors'][0],
                        'secondary_color' => $definition['colors'][1],
                        'background_color' => $definition['colors'][2],
                        'text_color' => $definition['colors'][3],
                        'store_theme' => 'custom',
                        'dashboard_theme' => 'blush',
                        'is_active' => true,
                        'onboarding_step' => 5,
                        'onboarding_completed_at' => now(),
                    ]
                );

                $this->seedCatalog($store, $definition['categories']);
                $this->seedOrders($store, $index);

                $credentials[] = [$store->name, url('/' . $store->slug), $email, $password];
            }
        });

        $this->command?->newLine();
        $this->command?->info('Lojas de exemplo criadas ou atualizadas.');
        $this->command?->table(['Loja', 'Vitrine', 'Login', 'Senha'], $credentials);
    }

    private function seedCatalog(Store $store, array $categories): void
    {
        foreach ($categories as $categoryName => $products) {
            $category = Category::updateOrCreate(
                ['store_id' => $store->id, 'slug' => Str::slug($categoryName)],
                ['name' => $categoryName]
            );

            foreach ($products as $index => [$name, $price, $description]) {
                $product = Product::updateOrCreate(
                    ['store_id' => $store->id, 'slug' => Str::slug($name)],
                    [
                        'category_id' => $category->id,
                        'name' => $name,
                        'description' => $description,
                        'price' => $price,
                        'is_active' => true,
                        'is_featured' => $index === 0,
                        'availability_status' => 'pronta_entrega',
                        'stock_quantity' => 12,
                        'track_stock' => true,
                    ]
                );

                $product->categories()->syncWithoutDetaching([$category->id]);
            }
        }
    }

    private function seedOrders(Store $store, int $storeIndex): void
    {
        $products = $store->products()->orderBy('id')->get();
        $statuses = [
            'pendente',
            'em andamento',
            "conclu\u{00ED}do",
            "conclu\u{00ED}do",
            "conclu\u{00ED}do",
            'cancelado',
        ];
        $customers = [
            ['Mariana Souza', 'Rua das Flores, 120', 'Entregar no periodo da tarde.'],
            ['Lucas Oliveira', 'Avenida Central, 45', 'Chamar no WhatsApp ao chegar.'],
            ['Fernanda Lima', 'Rua do Mercado, 88', null],
            ['Rafael Santos', 'Alameda Verde, 310', 'Pedido para presente.'],
            ['Juliana Costa', 'Rua Aurora, 27', null],
            ['Paulo Mendes', 'Avenida Brasil, 540', 'Sem observacoes adicionais.'],
        ];
        $daysAgo = [0, 2, 5, 11, 18, 32];

        foreach ($statuses as $orderIndex => $status) {
            $firstProduct = $products[$orderIndex % $products->count()];
            $secondProduct = $products[($orderIndex + 1) % $products->count()];
            $firstQuantity = ($orderIndex % 2) + 1;
            $secondQuantity = 1;
            $total = ((float) $firstProduct->price * $firstQuantity)
                + ((float) $secondProduct->price * $secondQuantity);
            $whatsapp = '55119' . str_pad((string) ($storeIndex * 100000 + $orderIndex + 1), 8, '0', STR_PAD_LEFT);

            $order = Order::updateOrCreate(
                ['store_id' => $store->id, 'customer_whatsapp' => $whatsapp],
                [
                    'customer_name' => $customers[$orderIndex][0],
                    'customer_address' => $customers[$orderIndex][1],
                    'notes' => $customers[$orderIndex][2],
                    'total' => $total,
                    'status' => $status,
                ]
            );

            $order->items()->delete();
            $order->items()->createMany([
                [
                    'product_id' => $firstProduct->id,
                    'product_name' => $firstProduct->name,
                    'price' => $firstProduct->price,
                    'quantity' => $firstQuantity,
                    'subtotal' => (float) $firstProduct->price * $firstQuantity,
                ],
                [
                    'product_id' => $secondProduct->id,
                    'product_name' => $secondProduct->name,
                    'price' => $secondProduct->price,
                    'quantity' => $secondQuantity,
                    'subtotal' => (float) $secondProduct->price * $secondQuantity,
                ],
            ]);

            $createdAt = now()->subDays($daysAgo[$orderIndex])->subHours($storeIndex);
            DB::table('orders')->where('id', $order->id)->update([
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }

    private function stores(): array
    {
        return [
            [
                'owner' => 'Ana Demo',
                'name' => 'Doces da Vila',
                'slug' => 'demo-doces-da-vila',
                'description' => 'Doces artesanais preparados para presentear e compartilhar.',
                'colors' => ['#D9467D', '#F9A8C4', '#FFF7FA', '#3F1D2B'],
                'categories' => [
                    'Brigadeiros' => [
                        ['Caixa de brigadeiros', 24.90, 'Seis brigadeiros artesanais em sabores variados.'],
                        ['Brigadeiro gourmet', 4.50, 'Brigadeiro cremoso com chocolate selecionado.'],
                    ],
                    'Presentes' => [
                        ['Kit carinho', 49.90, 'Selecao de doces pronta para presentear.'],
                        ['Mini festa', 79.90, 'Kit de doces para pequenas comemoracoes.'],
                    ],
                ],
            ],
            [
                'owner' => 'Bruno Demo',
                'name' => 'Cafe do Bairro',
                'slug' => 'demo-cafe-do-bairro',
                'description' => 'Cafes especiais, acompanhamentos e kits para o dia a dia.',
                'colors' => ['#166534', '#F59E0B', '#F7FEE7', '#1F2937'],
                'categories' => [
                    'Cafes' => [
                        ['Cafe especial 250g', 32.00, 'Graos selecionados com torra media.'],
                        ['Cafe moido 500g', 48.00, 'Cafe equilibrado e pronto para coar.'],
                    ],
                    'Kits' => [
                        ['Kit degustacao', 69.00, 'Tres perfis de cafe para conhecer novos sabores.'],
                        ['Cafe e caneca', 89.00, 'Cafe especial acompanhado de caneca.'],
                    ],
                ],
            ],
            [
                'owner' => 'Carla Demo',
                'name' => 'Atelie Aurora',
                'slug' => 'demo-atelie-aurora',
                'description' => 'Pecas autorais feitas a mao para deixar a rotina mais bonita.',
                'colors' => ['#7C3AED', '#14B8A6', '#FAFAF9', '#292524'],
                'categories' => [
                    'Decoracao' => [
                        ['Vaso artesanal', 59.90, 'Vaso decorativo produzido e finalizado a mao.'],
                        ['Bandeja organica', 44.90, 'Bandeja leve para organizar pequenos objetos.'],
                    ],
                    'Papelaria' => [
                        ['Caderno Aurora', 36.00, 'Caderno artesanal com capa exclusiva.'],
                        ['Marcadores coloridos', 18.00, 'Conjunto com quatro marcadores artesanais.'],
                    ],
                ],
            ],
            [
                'owner' => 'Diego Demo',
                'name' => 'Verde em Casa',
                'slug' => 'demo-verde-em-casa',
                'description' => 'Plantas e cuidados simples para trazer mais verde para perto.',
                'colors' => ['#15803D', '#84CC16', '#F0FDF4', '#14532D'],
                'categories' => [
                    'Plantas' => [
                        ['Jiboia pequena', 29.90, 'Planta jovem e facil de cuidar.'],
                        ['Suculenta sortida', 14.90, 'Suculenta selecionada conforme disponibilidade.'],
                    ],
                    'Cuidados' => [
                        ['Kit jardinagem', 54.90, 'Ferramentas essenciais para vasos e pequenos jardins.'],
                        ['Adubo organico', 19.90, 'Adubo pronto para fortalecer plantas domesticas.'],
                    ],
                ],
            ],
            [
                'owner' => 'Elisa Demo',
                'name' => 'Movimento Leve',
                'slug' => 'demo-movimento-leve',
                'description' => 'Acessorios praticos para treino, alongamento e bem-estar.',
                'colors' => ['#0284C7', '#F97316', '#F0F9FF', '#172554'],
                'categories' => [
                    'Treino' => [
                        ['Faixa elastica', 27.90, 'Faixa de resistencia para diversos exercicios.'],
                        ['Corda de pular', 39.90, 'Corda ajustavel para treino cardiovascular.'],
                    ],
                    'Bem-estar' => [
                        ['Tapete de yoga', 89.90, 'Tapete confortavel com superficie aderente.'],
                        ['Rolo de massagem', 64.90, 'Rolo para relaxamento e recuperacao muscular.'],
                    ],
                ],
            ],
        ];
    }
}
