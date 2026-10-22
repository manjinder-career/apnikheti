<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    public static function get(string $key, ?string $default = null): ?string
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function isEnabled(string $key, bool $default = false): bool
    {
        $setting = static::where('key', $key)->first();
        if ($setting === null || $setting->value === null) {
            return $default;
        }
        return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
    }

    public static function isSimpleUpiEnabled(): bool
    {
        return static::isEnabled('enable_simple_upi', true);
    }

    public static function isRazorpayEnabled(): bool
    {
        return static::isEnabled('enable_razorpay', true);
    }

    public static function set(string $key, ?string $value, string $group = 'general'): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
    }
}
