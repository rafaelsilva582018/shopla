<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['category_id', 'product_id']);
        });

        DB::table('products')
            ->whereNotNull('category_id')
            ->orderBy('id')
            ->select(['id', 'category_id'])
            ->chunk(500, function ($products) {
                $now = now();

                DB::table('category_product')->insertOrIgnore(
                    $products->map(fn ($product) => [
                        'category_id' => $product->category_id,
                        'product_id' => $product->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])->all()
                );
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_product');
    }
};
