<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopProduct extends Model
{
    protected $fillable = [
        'sku',
        'name',
        'product_type',
        'shop_product_category_id',
        'description',
        'price',
        'stock_quantity',
        'unit',
        'weight',
        'weight_grams',
        'image_url',
        'is_available',
        'is_published',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'weight_grams' => 'integer',
        'is_available' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function getWeightInGrams(): int
    {
        if ($this->weight_grams !== null && $this->weight_grams > 0) {
            return (int) $this->weight_grams;
        }

        if (! empty($this->weight)) {
            $str = strtolower(trim($this->weight));
            if (preg_match('/([\d\.]+)\s*(kg|kilo|kilogram)/', $str, $matches)) {
                return (int) round(((float) $matches[1]) * 1000);
            }
            if (preg_match('/([\d\.]+)\s*(g|gm|gram|grams)/', $str, $matches)) {
                return (int) round((float) $matches[1]);
            }
            if (str_contains($str, 'box')) {
                return 1000;
            }
            if (is_numeric($str)) {
                return (int) $str;
            }
        }

        return 100; // default 100g for small seed packets
    }

    public function formattedWeight(): string
    {
        if (! empty($this->weight)) {
            return $this->weight;
        }

        $grams = $this->getWeightInGrams();
        if ($grams >= 1000) {
            $kg = $grams / 1000;
            return ($kg == (int) $kg ? (int) $kg : number_format($kg, 1)) . ' Kg';
        }

        return $grams . 'g';
    }

    public function isInStock(): bool
    {
        return $this->is_available && $this->stock_quantity > 0;
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity > 0 && $this->stock_quantity <= 5;
    }

    public function hasStock(int $quantity = 1): bool
    {
        return $this->is_available && $this->stock_quantity >= $quantity;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ShopProductCategory::class, 'shop_product_category_id');
    }

    public function orderItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class, 'shop_product_id');
    }

    public function photoUrl(): ?string
    {
        if (! $this->image_url) {
            return null;
        }

        if (str_starts_with($this->image_url, 'http://') || str_starts_with($this->image_url, 'https://')) {
            return $this->image_url;
        }

        return asset($this->image_url);
    }

    public function hasUploadedPhoto(): bool
    {
        return is_string($this->image_url) && str_starts_with($this->image_url, 'storage/shop-products/');
    }
}
