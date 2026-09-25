<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    protected $table = 'platform_settings';

    protected $fillable = ['key', 'value', 'value_type', 'updated_by_user_id'];

    public static function valueOf(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->first();
        if (! $setting) {
            return $default;
        }

        return match ($setting->value_type) {
            'BOOLEAN' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'INTEGER' => (int) $setting->value,
            'DECIMAL' => (float) $setting->value,
            default => $setting->value,
        };
    }

    public static function put(string $key, mixed $value, string $type = 'string', ?int $updatedBy = null): self
    {
        $stored = match (strtolower($type)) {
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };

        $valueType = match (strtolower($type)) {
            'boolean' => 'BOOLEAN',
            'integer' => 'INTEGER',
            'float', 'decimal' => 'DECIMAL',
            default => 'STRING',
        };

        return static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $stored, 'value_type' => $valueType, 'updated_by_user_id' => $updatedBy]
        );
    }

    public static function commissionRate(): float
    {
        $default = (float) config('likhae.seller_commission_rate', 0.10);
        return min(1, max(0, (float) static::valueOf('seller_commission_rate', $default)));
    }

    public static function commissionBps(): int
    {
        return min(10000, max(0, (int) static::valueOf('default_commission_bps', 800)));
    }
}
