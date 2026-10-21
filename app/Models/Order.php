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
        'transaction_id',
        'payment_verified_at',
        'upi_id',
        'payment_notes',
        'courier_name',
        'tracking_number',
        'customer_notes',
        'admin_notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'payment_verified_at' => 'datetime',
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

    public function getUpiId(): string
    {
        return $this->upi_id ?: (SiteSetting::get('upi_id') ?: 'apnikheti@sbi');
    }

    public function getUpiPayeeName(): string
    {
        return SiteSetting::get('upi_payee_name') ?: 'Apni Kheti Apne Beej';
    }

    public function getUpiQrCodeUrl(): ?string
    {
        $qrPath = SiteSetting::get('upi_qr_code');
        if ($qrPath && file_exists(public_path('storage/' . $qrPath))) {
            return asset('storage/' . $qrPath);
        }
        return null;
    }

    public function getDynamicUpiQrUrl(): string
    {
        $upiId = $this->getUpiId();
        $name = urlencode($this->getUpiPayeeName());
        $amount = number_format((float) $this->total_amount, 2, '.', '');
        $note = urlencode('Order ' . $this->order_number);
        
        $upiString = "upi://pay?pa={$upiId}&pn={$name}&am={$amount}&cu=INR&tn={$note}";
        return 'https://api.qrserver.com/v1/create-qr-code/?size=260x260&margin=10&data=' . urlencode($upiString);
    }
}
