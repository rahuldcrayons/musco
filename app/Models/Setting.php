<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'is_public',
    ];

    /**
     * Keys that should be encrypted at rest in the database.
     */
    protected static array $encryptedKeys = [
        'razorpay_key_secret',
        'razorpay_webhook_secret',
        'mail_password',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    public function getValueAttribute($value)
    {
        // Decrypt sensitive values
        if (in_array($this->key, static::$encryptedKeys) && $value) {
            try {
                $value = Crypt::decryptString($value);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                // Value may not be encrypted yet (legacy data), return as-is
            }
        }

        return match ($this->type) {
            'integer' => (int) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json', 'array' => json_decode($value, true),
            default => $value,
        };
    }

    public function setValueAttribute($value): void
    {
        $raw = match ($this->type) {
            'json', 'array' => json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };

        // Encrypt sensitive values before storing
        if (in_array($this->key, static::$encryptedKeys) && $raw) {
            $raw = Crypt::encryptString($raw);
        }

        $this->attributes['value'] = $raw;
    }

    public static function get(string $key, $default = null)
    {
        // Load ALL settings in one query, cache for 1 hour
        // Uses ->get() instead of pluck() so accessors (decrypt, type-cast) run
        $all = Cache::remember('settings.all', 3600, function () {
            return static::all(['key', 'value', 'type'])->pluck('value', 'key')->toArray();
        });

        return $all[$key] ?? $default;
    }

    public static function set(string $key, $value, string $type = 'string', string $group = 'general'): self
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'group' => $group]
        );

        Cache::forget('settings.all');

        return $setting;
    }

    public static function getGroup(string $group): array
    {
        return Cache::remember("settings.group.{$group}", 3600, function () use ($group) {
            return static::where('group', $group)
                ->pluck('value', 'key')
                ->toArray();
        });
    }
}
