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
                        'plan' => 'plus',
                        'plan_started_at' => now(),
                        'onboarding_plan' => 'plus',
                    ]
                );

                $store = Store::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name' => $definition['name'],
                        'slug' => $definition['slug'],
                        'whatsapp' => '5511999999999',
                        'instagram' => str_replace('-', '', $definition['slug']),
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
                        ['Brigadeiro de pistache', 6.50, 'Pistache selecionado e acabamento delicado.'],
                        ['Brigadeiro de churros', 5.50, 'Doce de leite, canela e acucar.'],
                    ],
                    'Bolos' => [
                        ['Bolo de chocolate', 64.90, 'Bolo macio com cobertura cremosa.'],
                        ['Bolo de cenoura', 54.90, 'Receita caseira com calda de chocolate.'],
                        ['Bolo red velvet', 79.90, 'Massa aveludada com creme suave.'],
                        ['Bolo de limao', 49.90, 'Bolo leve com cobertura citrica.'],
                    ],
                    'Sobremesas' => [
                        ['Brownie tradicional', 12.00, 'Brownie intenso com casquinha crocante.'],
                        ['Cookie recheado', 14.00, 'Cookie macio com recheio de chocolate.'],
                        ['Pudim individual', 11.90, 'Pudim cremoso com calda de caramelo.'],
                        ['Torta de morango', 18.90, 'Fatia com creme e morangos frescos.'],
                    ],
                    'Presentes' => [
                        ['Kit carinho', 49.90, 'Selecao de doces pronta para presentear.'],
                        ['Mini festa', 79.90, 'Kit de doces para pequenas comemoracoes.'],
                        ['Caixa aniversario', 94.90, 'Doces, brownie e mensagem personalizada.'],
                        ['Cesta doce', 119.90, 'Cesta completa para momentos especiais.'],
                    ],
                    'Festas' => [
                        ['Cento de docinhos', 145.00, 'Cem docinhos tradicionais para eventos.'],
                        ['Kit festa para 10', 189.90, 'Bolo e doces para dez convidados.'],
                        ['Kit festa para 20', 329.90, 'Bolo e doces para vinte convidados.'],
                        ['Topper personalizado', 25.00, 'Topper com nome e tema da comemoracao.'],
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
                        ['Cafe especial 1kg', 84.00, 'Pacote economico de graos especiais.'],
                        ['Descafeinado 250g', 36.00, 'Cafe suave e aromatico sem cafeina.'],
                    ],
                    'Bebidas' => [
                        ['Cappuccino classico', 14.00, 'Cafe, leite cremoso e toque de canela.'],
                        ['Chocolate quente', 16.00, 'Chocolate intenso e cremoso.'],
                        ['Cold brew', 15.00, 'Cafe extraido a frio, leve e refrescante.'],
                        ['Cha gelado artesanal', 12.00, 'Cha da casa com frutas e pouco acucar.'],
                    ],
                    'Acompanhamentos' => [
                        ['Pao de queijo', 8.50, 'Porcao quentinha com cinco unidades.'],
                        ['Croissant', 12.00, 'Massa folhada amanteigada.'],
                        ['Bolo do dia', 10.00, 'Fatia de bolo caseiro selecionado.'],
                        ['Cookie de chocolate', 9.00, 'Cookie artesanal com gotas de chocolate.'],
                    ],
                    'Kits' => [
                        ['Kit degustacao', 69.00, 'Tres perfis de cafe para conhecer novos sabores.'],
                        ['Cafe e caneca', 89.00, 'Cafe especial acompanhado de caneca.'],
                        ['Kit coador', 109.00, 'Cafe, suporte, filtro e jarra para preparo.'],
                        ['Assinatura degustacao', 129.00, 'Selecao mensal com tres cafes especiais.'],
                    ],
                    'Acessorios' => [
                        ['Caneca da casa', 34.90, 'Caneca resistente com identidade da cafeteria.'],
                        ['Coador individual', 39.90, 'Suporte pratico para uma xicara.'],
                        ['Prensa francesa', 119.90, 'Prensa de vidro para cafe encorpado.'],
                        ['Garrafa termica', 149.90, 'Garrafa compacta que conserva a temperatura.'],
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
                        ['Porta-vela Aurora', 34.90, 'Peca artesanal para velas pequenas.'],
                        ['Escultura minimalista', 89.90, 'Objeto decorativo de linhas organicas.'],
                    ],
                    'Papelaria' => [
                        ['Caderno Aurora', 36.00, 'Caderno artesanal com capa exclusiva.'],
                        ['Marcadores coloridos', 18.00, 'Conjunto com quatro marcadores artesanais.'],
                        ['Planner semanal', 42.00, 'Planejamento pratico para a semana.'],
                        ['Bloco de notas', 16.00, 'Bloco compacto com folhas destacaveis.'],
                    ],
                    'Ceramica' => [
                        ['Caneca organica', 72.00, 'Caneca modelada e esmaltada a mao.'],
                        ['Prato decorativo', 68.00, 'Prato artesanal para servir ou decorar.'],
                        ['Bowl pequeno', 54.00, 'Tigela versatil com acabamento exclusivo.'],
                        ['Dupla de xicaras', 96.00, 'Duas xicaras artesanais para cafe.'],
                    ],
                    'Textil' => [
                        ['Ecobag estampada', 39.90, 'Bolsa reutilizavel com estampa autoral.'],
                        ['Capa de almofada', 49.90, 'Capa decorativa em tecido resistente.'],
                        ['Pano de mesa', 69.90, 'Peca leve para compor a mesa.'],
                        ['Necessaire', 42.90, 'Necessaire forrada com fechamento em ziper.'],
                    ],
                    'Presentes' => [
                        ['Kit escritorio', 79.90, 'Caderno, bloco e marcadores coordenados.'],
                        ['Kit casa nova', 139.90, 'Selecao de ceramicas e decoracao.'],
                        ['Embalagem especial', 15.00, 'Embalagem artesanal com mensagem.'],
                        ['Vale-presente', 100.00, 'Credito para escolher produtos do atelie.'],
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
                        ['Zamioculca', 49.90, 'Planta resistente para ambientes internos.'],
                        ['Espada de Sao Jorge', 34.90, 'Planta de baixa manutencao e grande resistencia.'],
                    ],
                    'Vasos' => [
                        ['Vaso ceramico P', 32.00, 'Vaso compacto com acabamento fosco.'],
                        ['Vaso ceramico M', 49.00, 'Vaso versatil para plantas medias.'],
                        ['Cachepo de palha', 44.90, 'Cachepo natural para ambientes acolhedores.'],
                        ['Vaso autoirrigavel', 59.90, 'Vaso com reservatorio para facilitar os cuidados.'],
                    ],
                    'Cuidados' => [
                        ['Kit jardinagem', 54.90, 'Ferramentas essenciais para vasos e pequenos jardins.'],
                        ['Adubo organico', 19.90, 'Adubo pronto para fortalecer plantas domesticas.'],
                        ['Substrato premium 2kg', 24.90, 'Mistura leve e nutritiva para vasos.'],
                        ['Borrifador', 18.90, 'Borrifador compacto para regas delicadas.'],
                    ],
                    'Temperos' => [
                        ['Manjericao', 12.90, 'Muda aromatica pronta para cultivar.'],
                        ['Alecrim', 12.90, 'Muda resistente e muito perfumada.'],
                        ['Hortela', 11.90, 'Muda fresca para bebidas e receitas.'],
                        ['Kit horta em casa', 64.90, 'Vasos, sementes e substrato para comecar.'],
                    ],
                    'Presentes' => [
                        ['Terrario pequeno', 69.90, 'Mini jardim montado em recipiente de vidro.'],
                        ['Kit boas energias', 89.90, 'Planta, vaso e cartao para presentear.'],
                        ['Trio de suculentas', 49.90, 'Tres suculentas em vasos coordenados.'],
                        ['Vale-presente verde', 100.00, 'Credito para escolher plantas e acessorios.'],
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
                        ['Mini band kit', 49.90, 'Tres faixas com diferentes resistencias.'],
                        ['Par de halteres 2kg', 79.90, 'Halteres revestidos para treino funcional.'],
                    ],
                    'Bem-estar' => [
                        ['Tapete de yoga', 89.90, 'Tapete confortavel com superficie aderente.'],
                        ['Rolo de massagem', 64.90, 'Rolo para relaxamento e recuperacao muscular.'],
                        ['Bola de massagem', 24.90, 'Bola firme para liberacao de pontos de tensao.'],
                        ['Almofada de meditacao', 94.90, 'Apoio confortavel para meditacao e respiracao.'],
                    ],
                    'Hidratacao' => [
                        ['Garrafa 750ml', 44.90, 'Garrafa leve com marcador de volume.'],
                        ['Squeeze termico', 89.90, 'Garrafa termica compacta para treinos.'],
                        ['Copo com canudo', 39.90, 'Copo reutilizavel com tampa segura.'],
                        ['Kit hidratacao', 119.90, 'Garrafa termica e bolsa de transporte.'],
                    ],
                    'Alongamento' => [
                        ['Bloco de yoga', 29.90, 'Bloco firme para apoio e alinhamento.'],
                        ['Cinto de alongamento', 32.90, 'Faixa ajustavel para ampliar movimentos.'],
                        ['Roda de yoga', 129.90, 'Acessorio para mobilidade e abertura corporal.'],
                        ['Kit alongamento', 74.90, 'Bloco, faixa e guia de exercicios.'],
                    ],
                    'Acessorios' => [
                        ['Bolsa para tapete', 49.90, 'Bolsa leve com alca ajustavel.'],
                        ['Toalha esportiva', 34.90, 'Toalha compacta de secagem rapida.'],
                        ['Munhequeira', 29.90, 'Par de munhequeiras para suporte no treino.'],
                        ['Pochete esportiva', 54.90, 'Pochete ajustavel para celular e chaves.'],
                    ],
                ],
            ],
        ];
    }
}
