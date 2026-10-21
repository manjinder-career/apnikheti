<?php

namespace App\Services;

use App\Models\ShopProduct;
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

    public function shippingCost(): float
    {
        $subtotal = $this->subtotal();
        if ($subtotal <= 0) {
            return 0.00;
        }

        return $subtotal >= self::FREE_SHIPPING_THRESHOLD ? 0.00 : self::FLAT_SHIPPING_RATE;
    }

    public function total(): float
    {
        return round($this->subtotal() + $this->shippingCost(), 2);
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
