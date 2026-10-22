<?php

namespace App\Services;

use App\Models\ShopProduct;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected const SESSION_KEY = 'apni_kheti_cart';
    public const FREE_SHIPPING_THRESHOLD = 500.00;
    public const FLAT_SHIPPING_RATE = 50.00;

    public function getItems(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public function add(ShopProduct $product, int $quantity = 1): array
    {
        if (! $product->is_published || ! $product->is_available || $product->stock_quantity <= 0) {
            return [
                'success' => false,
                'message' => 'Sorry, "' . $product->name . '" is currently out of stock.',
            ];
        }

        $items = $this->getItems();
        $currentQty = isset($items[$product->id]) ? $items[$product->id]['quantity'] : 0;
        $newQty = $currentQty + $quantity;

        if ($newQty > $product->stock_quantity) {
            $available = max(0, $product->stock_quantity - $currentQty);
            return [
                'success' => false,
                'message' => 'Only ' . $product->stock_quantity . ' units of "' . $product->name . '" available in stock' . ($currentQty > 0 ? ' (you already have ' . $currentQty . ' in cart).' : '.'),
            ];
        }

        $items[$product->id] = [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku ?? ('SKU-' . $product->id),
            'price' => (float) $product->price,
            'quantity' => $newQty,
            'unit' => $product->unit ?? 'packet',
            'weight' => $product->weight,
            'weight_grams' => $product->getWeightInGrams(),
            'image_url' => $product->photoUrl(),
            'stock' => $product->stock_quantity,
        ];

        Session::put(self::SESSION_KEY, $items);

        return [
            'success' => true,
            'message' => 'Added "' . $product->name . '" to your cart.',
            'count' => $this->count(),
            'total' => $this->total(),
        ];
    }

    public function update(int $productId, int $quantity): array
    {
        $items = $this->getItems();

        if (! isset($items[$productId])) {
            return ['success' => false, 'message' => 'Item not found in cart.'];
        }

        if ($quantity <= 0) {
            return $this->remove($productId);
        }

        $product = ShopProduct::find($productId);
        if (! $product || ! $product->is_available) {
            unset($items[$productId]);
            Session::put(self::SESSION_KEY, $items);
            return ['success' => false, 'message' => 'Product is no longer available and was removed from cart.'];
        }

        if ($quantity > $product->stock_quantity) {
            $items[$productId]['quantity'] = $product->stock_quantity;
            Session::put(self::SESSION_KEY, $items);
            return [
                'success' => false,
                'message' => 'Quantity adjusted to maximum available stock (' . $product->stock_quantity . ').',
            ];
        }

        $items[$productId]['quantity'] = $quantity;
        $items[$productId]['price'] = (float) $product->price;
        $items[$productId]['stock'] = $product->stock_quantity;
        Session::put(self::SESSION_KEY, $items);

        return ['success' => true, 'message' => 'Cart updated.'];
    }

    public function remove(int $productId): array
    {
        $items = $this->getItems();
        $name = $items[$productId]['name'] ?? 'Item';
        unset($items[$productId]);
        Session::put(self::SESSION_KEY, $items);

        return [
            'success' => true,
            'message' => 'Removed "' . $name . '" from your cart.',
        ];
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function count(): int
    {
        $count = 0;
        foreach ($this->getItems() as $item) {
            $count += (int) $item['quantity'];
        }
        return $count;
    }

    public function subtotal(): float
    {
        $subtotal = 0.0;
        foreach ($this->getItems() as $item) {
            $subtotal += ((float) $item['price']) * ((int) $item['quantity']);
        }
        return round($subtotal, 2);
    }

    public function totalWeightInGrams(): int
    {
        $totalGrams = 0;
        foreach ($this->getItems() as $id => $item) {
            $qty = (int) ($item['quantity'] ?? 1);
            if (! empty($item['weight_grams'])) {
                $totalGrams += ((int) $item['weight_grams']) * $qty;
            } else {
                $product = ShopProduct::find($id);
                $grams = $product ? $product->getWeightInGrams() : 100;
                $totalGrams += $grams * $qty;
            }
        }
        return $totalGrams;
    }

    public function totalWeightInKg(): float
    {
        return round($this->totalWeightInGrams() / 1000, 2);
    }

    public function formattedTotalWeight(): string
    {
        $grams = $this->totalWeightInGrams();
        if ($grams >= 1000) {
            $kg = $grams / 1000;
            return ($kg == (int) $kg ? (int) $kg : number_format($kg, 2)) . ' Kg';
        }
        return $grams . 'g';
    }

    public function isPunjab(?string $state): bool
    {
        if (empty($state)) {
            return true;
        }
        return strcasecmp(trim($state), 'punjab') === 0;
    }

    public function freeShippingThreshold(?string $state = null): float
    {
        $isPunjab = $this->isPunjab($state);
        $settingKey = $isPunjab ? 'shipping_punjab_free_threshold' : 'shipping_outside_free_threshold';
        $settingVal = SiteSetting::get($settingKey);

        if ($settingVal !== null && $settingVal !== '') {
            return (float) $settingVal;
        }

        return $isPunjab ? self::FREE_SHIPPING_THRESHOLD : 800.00;
    }

    public function amountNeededForFreeShipping(?string $state = null): float
    {
        $threshold = $this->freeShippingThreshold($state);
        if ($threshold <= 0) {
            return 0.00;
        }
        return (float) max(0, round($threshold - $this->subtotal(), 2));
    }

    public function shippingCost(?string $state = null): float
    {
        $subtotal = $this->subtotal();
        if ($subtotal <= 0) {
            return 0.00;
        }

        $isPunjab = $this->isPunjab($state);

        // Check if free shipping threshold is satisfied
        $freeThreshold = $this->freeShippingThreshold($state);
        if ($freeThreshold > 0 && $subtotal >= $freeThreshold) {
            return 0.00;
        }

        // Base rate (covers up to 1000g / 1 Kg)
        $baseSettingKey = $isPunjab ? 'shipping_punjab_base_cost' : 'shipping_outside_base_cost';
        $baseVal = SiteSetting::get($baseSettingKey);
        $baseCost = ($baseVal !== null && $baseVal !== '') ? (float) $baseVal : ($isPunjab ? self::FLAT_SHIPPING_RATE : 80.00);

        // Per additional Kg rate
        $perKgSettingKey = $isPunjab ? 'shipping_punjab_per_kg' : 'shipping_outside_per_kg';
        $perKgVal = SiteSetting::get($perKgSettingKey);
        $perKgCost = ($perKgVal !== null && $perKgVal !== '') ? (float) $perKgVal : ($isPunjab ? 30.00 : 50.00);

        $totalGrams = $this->totalWeightInGrams();

        // If total weight is up to 1000g (1 Kg), charge only the base rate
        if ($totalGrams <= 1000) {
            return round($baseCost, 2);
        }

        // Additional kilograms rounded up to nearest whole Kg (standard courier billing slab)
        $extraGrams = $totalGrams - 1000;
        $extraKg = (int) ceil($extraGrams / 1000);
        $cost = $baseCost + ($extraKg * $perKgCost);

        return round($cost, 2);
    }

    public function total(?string $state = null): float
    {
        return round($this->subtotal() + $this->shippingCost($state), 2);
    }

    public function shippingBreakdown(?string $state = null): array
    {
        $isPunjab = $this->isPunjab($state);
        $totalGrams = $this->totalWeightInGrams();
        $totalKg = $this->totalWeightInKg();
        $cost = $this->shippingCost($state);
        $freeThreshold = $this->freeShippingThreshold($state);
        $isFree = ($cost === 0.00 && $this->subtotal() > 0);

        return [
            'is_punjab' => $isPunjab,
            'region_label' => $isPunjab ? 'Punjab (Local)' : 'Outside Punjab (National)',
            'total_grams' => $totalGrams,
            'total_kg' => $totalKg,
            'formatted_weight' => $this->formattedTotalWeight(),
            'shipping_cost' => $cost,
            'free_shipping_threshold' => $freeThreshold,
            'is_free' => $isFree,
            'amount_needed_for_free' => $this->amountNeededForFreeShipping($state),
        ];
    }

    public function syncWithDatabase(): array
    {
        $items = $this->getItems();
        $messages = [];
        $changed = false;

        foreach ($items as $id => $item) {
            $product = ShopProduct::find($id);
            if (! $product || ! $product->is_published || ! $product->is_available || $product->stock_quantity <= 0) {
                unset($items[$id]);
                $messages[] = '"' . $item['name'] . '" is no longer available and was removed.';
                $changed = true;
                continue;
            }

            if ($item['quantity'] > $product->stock_quantity) {
                $items[$id]['quantity'] = $product->stock_quantity;
                $messages[] = 'Quantity for "' . $product->name . '" was reduced to available stock (' . $product->stock_quantity . ').';
                $changed = true;
            }

            if ((float) $items[$id]['price'] !== (float) $product->price) {
                $items[$id]['price'] = (float) $product->price;
                $changed = true;
            }
        }

        if ($changed) {
            Session::put(self::SESSION_KEY, $items);
        }

        return $messages;
    }
}
