<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AsaasWebhookController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PlanCheckoutController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicStoreController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceController;

/*
|--------------------------------------------------------------------------
| Página inicial
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Dashboard do lojista
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::post('/webhooks/asaas', AsaasWebhookController::class)->name('webhooks.asaas');

/*
|--------------------------------------------------------------------------
| Rotas protegidas - somente usuário logado
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::get('/usuarios', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/usuarios/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::patch('/usuarios/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::get('/configuracoes', [AdminSettingsController::class, 'index'])->name('settings.index');
        Route::put('/configuracoes', [AdminSettingsController::class, 'update'])->name('settings.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Perfil do usuário
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/painel', [ProfileController::class, 'updateDashboardTheme'])->name('profile.dashboard-theme.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/assinatura', [SubscriptionController::class, 'destroy'])->name('subscription.cancel');
    Route::delete('/notificacoes', [NotificationController::class, 'destroy'])->name('notifications.dismiss');
    Route::post('/notificacoes/limpar', [NotificationController::class, 'clear'])->name('notifications.clear');

    Route::get('/planos', [PlanController::class, 'index'])->name('plans.index');
    Route::post('/planos/{plan}/checkout', [PlanCheckoutController::class, 'store'])->name('plans.checkout');
    Route::get('/planos/retorno/{status}', [PlanCheckoutController::class, 'result'])->name('plans.return');

    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::get('/onboarding/slug-disponivel', [OnboardingController::class, 'checkSlug'])->name('onboarding.slug-check');
    Route::post('/onboarding/painel', [OnboardingController::class, 'panel'])->name('onboarding.panel');
    Route::post('/onboarding/plano', [OnboardingController::class, 'plan'])->name('onboarding.plan');
    Route::post('/onboarding/loja', [OnboardingController::class, 'store'])->name('onboarding.store');
    Route::post('/onboarding/categorias', [OnboardingController::class, 'categories'])->name('onboarding.categories');
    Route::post('/onboarding/categorias/pular', [OnboardingController::class, 'skipCategories'])->name('onboarding.categories.skip');
    Route::post('/onboarding/produto', [OnboardingController::class, 'product'])->name('onboarding.product');
    Route::post('/onboarding/produto/pular', [OnboardingController::class, 'skipProduct'])->name('onboarding.product.skip');
    Route::post('/onboarding/finalizar', [OnboardingController::class, 'finish'])->name('onboarding.finish');

    /*
    |--------------------------------------------------------------------------
    | Loja do usuário
    |--------------------------------------------------------------------------
    */
    Route::get('/minha-loja/criar', [StoreController::class, 'create'])->name('store.create');
    Route::post('/minha-loja/criar', [StoreController::class, 'store'])->name('store.store');

    /*
    |--------------------------------------------------------------------------
    | Configurações da loja
    |--------------------------------------------------------------------------
    */
    Route::get('/minha-loja/configuracoes', [StoreController::class, 'edit'])->name('store.edit');
    Route::put('/minha-loja/configuracoes', [StoreController::class, 'update'])->name('store.update');

    /*
    |--------------------------------------------------------------------------
    | Categorias
    |--------------------------------------------------------------------------
    */
    Route::get('/minha-loja/categorias', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/minha-loja/categorias', [CategoryController::class, 'store'])->name('categories.store');
    Route::delete('/minha-loja/categorias/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/minha-loja/categorias/{category}/editar', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/minha-loja/categorias/{category}', [CategoryController::class, 'update'])->name('categories.update');

    /*
    |--------------------------------------------------------------------------
    | Produtos
    |--------------------------------------------------------------------------
    */
    Route::get('/minha-loja/produtos', [ProductController::class, 'index'])->name('products.index');
    Route::get('/minha-loja/produtos/criar', [ProductController::class, 'create'])->name('products.create');
    Route::post('/minha-loja/produtos', [ProductController::class, 'store'])->name('products.store');
    Route::delete('/minha-loja/produtos/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    Route::get('/minha-loja/produtos/{product}/editar', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/minha-loja/produtos/{product}', [ProductController::class, 'update'])->name('products.update');

    /*
    |--------------------------------------------------------------------------
    | Estoque
    |--------------------------------------------------------------------------
    */
    Route::get('/minha-loja/estoque', [StockController::class, 'index'])->name('stock.index');
    Route::put('/minha-loja/estoque/{product}', [StockController::class, 'update'])->name('stock.update');

    /*
    |--------------------------------------------------------------------------
    | Pedidos
    |--------------------------------------------------------------------------
    */
    Route::get('/minha-loja/pedidos', [OrderController::class, 'index'])->name('orders.index');
    Route::put('/minha-loja/pedidos/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

     /*
    |--------------------------------------------------------------------------
    | Financeiro
    |--------------------------------------------------------------------------
    */
    Route::get('/minha-loja/financeiro', [FinanceController::class, 'index'])->name('finance.index');

    /*
    |--------------------------------------------------------------------------
    | Ranking
    |--------------------------------------------------------------------------
    */
    Route::get('/minha-loja/ranking', [RankingController::class, 'index'])->name('rankings.index');
});

/*
|--------------------------------------------------------------------------
| Rotas de autenticação do Breeze
|--------------------------------------------------------------------------
| IMPORTANTE:
| Isso precisa ficar antes da rota /{slug}.
| Senão /login seria interpretado como uma loja com slug "login".
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Loja pública
|--------------------------------------------------------------------------
| Essa rota precisa ser SEMPRE a última.
| Ela captura qualquer slug, exemplo:
| /teste
| /nanda-biscuit
| /minha-loja
|--------------------------------------------------------------------------
*/
Route::get('/{slug}', [PublicStoreController::class, 'show'])->name('store.public');
