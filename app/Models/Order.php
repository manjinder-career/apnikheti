<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_pincode',
        'subtotal',
        'shipping_cost',
        'total_amount',
        'payment_method',
        'payment_status',
        'status',
        'courier_name',
        'tracking_number',
        'customer_notes',
        'admin_notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'AK-' . date('Ymd') . '-' . strtoupper(Str::random(5));
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    public function canCancel(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Pending Confirmation',
            'processing' => 'Processing & Packing',
            'shipped' => 'Shipped',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    public function statusBadgeClasses(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
            'processing' => 'bg-sky-100 text-sky-800 border-sky-200',
            'shipped' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            'out_for_delivery' => 'bg-purple-100 text-purple-800 border-purple-200',
            'delivered' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }

    public function paymentStatusBadgeClasses(): string
    {
        return match ($this->payment_status) {
            'paid' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
            'failed' => 'bg-red-100 text-red-800 border-red-200',
            'refunded' => 'bg-gray-100 text-gray-800 border-gray-200',
            default => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }

    public function paymentMethodLabel(): string
    {
        return match ($this->payment_method) {
            'cod' => 'Cash / Pay on Delivery',
            'upi' => 'UPI / QR Payment',
            'bank_transfer' => 'Direct Bank Transfer',
            default => strtoupper($this->payment_method),
        };
    }
}
