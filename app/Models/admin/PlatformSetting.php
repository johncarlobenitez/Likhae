<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    protected $table = 'settings';

    protected $fillable = ['key', 'value', 'type', 'updated_by'];

    public static function valueOf(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->first();
        if (! $setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $setting->value,
            'float' => (float) $setting->value,
            'json' => json_decode((string) $setting->value, true) ?? $default,
            default => $setting->value,
        };
    }

    public static function put(string $key, mixed $value, string $type = 'string', ?int $updatedBy = null): self
    {
        $stored = match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => json_encode($value, JSON_THROW_ON_ERROR),
            default => (string) $value,
        };

        return static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $stored, 'type' => $type, 'updated_by' => $updatedBy]
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
