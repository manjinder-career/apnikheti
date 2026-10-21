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
        'image_url',
        'is_available',
        'is_published',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_available' => 'boolean',
        'is_published' => 'boolean',
    ];

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
