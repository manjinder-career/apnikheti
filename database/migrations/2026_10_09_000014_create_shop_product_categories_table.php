<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('shop_products', function (Blueprint $table) {
            $table->foreignId('shop_product_category_id')
                ->nullable()
                ->after('product_type')
                ->constrained('shop_product_categories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('shop_products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shop_product_category_id');
        });

        Schema::dropIfExists('shop_product_categories');
    }
};
