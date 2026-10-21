<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('pincode', 20)->nullable()->after('state');
        });

        Schema::table('shop_products', function (Blueprint $table) {
            $table->string('sku')->nullable()->unique()->after('id');
            $table->integer('stock_quantity')->default(50)->after('price');
            $table->string('unit')->default('packet')->after('stock_quantity');
            $table->string('weight')->nullable()->after('unit');
        });
    }

    public function down(): void
    {
        Schema::table('shop_products', function (Blueprint $table) {
            $table->dropColumn(['sku', 'stock_quantity', 'unit', 'weight']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'address', 'city', 'state', 'pincode']);
        });
    }
};
