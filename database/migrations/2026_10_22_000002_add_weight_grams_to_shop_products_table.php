<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shop_products', function (Blueprint $table) {
            $table->unsignedInteger('weight_grams')->nullable()->after('weight');
        });

        // Seed initial weight_grams for existing records based on their weight string
        $products = DB::table('shop_products')->get(['id', 'weight']);
        foreach ($products as $product) {
            $grams = 100; // default 100g for small seed packets
            if (! empty($product->weight)) {
                $str = strtolower(trim($product->weight));
                if (preg_match('/([\d\.]+)\s*(kg|kilo|kilogram)/', $str, $matches)) {
                    $grams = (int) round(((float) $matches[1]) * 1000);
                } elseif (preg_match('/([\d\.]+)\s*(g|gm|gram|grams)/', $str, $matches)) {
                    $grams = (int) round((float) $matches[1]);
                } elseif (str_contains($str, 'box')) {
                    $grams = 1000;
                }
            }

            DB::table('shop_products')->where('id', $product->id)->update(['weight_grams' => $grams]);
        }
    }

    public function down(): void
    {
        Schema::table('shop_products', function (Blueprint $table) {
            $table->dropColumn(['weight_grams']);
        });
    }
};
